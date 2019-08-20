<?php
namespace core\extension\parser\env;
use core\Heepp;
use core\extension\parser\env\exception\InvalidFileException;
use core\extension\parser\env\exception\InvalidPathException;

/**
 * This is the loaded class.
 * It's responsible for loading variables by reading a file from disk and:
 * - stripping comments beginning with a `#`,
 * - parsing lines that look shell variable setters, e.g `export key = value`, `key="value"`.
 */
class Loader {
    protected $filePath;
    protected $immutable;
    private $cacheResult = false;
    private $cachePath;
    private $envFilemTime;
    private $project;
    public function __construct($filePath,$immutable = false,$project = null) {
        $this->filePath  = $filePath;
        if(isset($project)) {
            $this->project = $project;
            $this->cachePath = __DIR__.DS.'cache'.DS.'env'.DS.$this->project.DS;
        } else {
            $this->cachePath = __DIR__.DS.'cache'.DS.'env'.DS;
        }

        if (!isset($_SERVER['envFilemTime'])) {
            $_SERVER['envFilemTime'] = filemtime($filePath);
        }

        $this->envFilemTime = $_SERVER['envFilemTime'];
        $this->immutable = $immutable;
    }

    public function setImmutable($immutable = false) {
        $this->immutable = $immutable;
        return $this;
    }

    public function getImmutable() {
        return $this->immutable;
    }

    public function load() {
        $this->ensureFileIsReadable();
        $filePath = $this->filePath;

        if ($this->getCachedValue($filePath) && $this->cacheResult) {
            return true;
        }
        $lines = $this->readLinesFromFile($filePath);
        foreach($lines as $line) {
            if (!$this->isComment($line) && $this->looksLikeSetter($line)) {
                $this->setEnvironmentVariable($line);
            }
        }

        if ($this->cacheResult) {
            file_put_contents($this->cachePath.'.env-'.$this->envFilemTime,serialize($_ENV));
        }
        return $lines;
    }

    private function getCachedValue($filePath) {
        $cachedFile = $this->cachePath.'.env-'.$this->envFilemTime;
        if (file_exists($cachedFile)) {
            $vars = unserialize(file_get_contents($cachedFile));
            foreach($vars as $key => $value) {
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
            }
            return true;
        }
        if (!file_exists($this->cachePath)) {
            mkdir($this->cachePath,0777,true);
        }
        return false;
    }

    protected function ensureFileIsReadable() {
        if (!is_readable($this->filePath) || !is_file($this->filePath)) {
            throw new InvalidPathException(sprintf('Unable to read the environment file at %s.',$this->filePath));
        }
    }

    /**
     * Normalise the given environment variable.
     * Takes value as passed in by developer and:
     * - ensures we're dealing with a separate name and value, breaking apart the name string if needed,
     * - cleaning the value of quotes,
     * - cleaning the name of quotes,
     * - resolving nested variables.
     *
     * @param string $name
     * @param string $value
     *
     * @return array
     * @throws InvalidFileException
     */
    protected function normaliseEnvironmentVariable($name,$value) {
        $returned = $this->splitCompoundStringIntoParts($name,$value);
        $returned = $this->splitCompoundStringIntoParts($returned[0],$returned[1]);
        $returned = $this->sanitiseVariableName($returned[0],$returned[1]);
        $returned = $this->sanitiseVariableValue($returned[0],$returned[1]);
        $returned = $this->makePathRealPath($returned[0],$returned[1]);
        $value = $this->resolveNestedVariables($returned[1]);
        if (strpos($value,'${') !== false) {
            $value = $this->resolveNestedVariables($value);
        }
        return [$returned[0],$value];
    }

    public function processFilters($name,$value) {
        $returned = $this->splitCompoundStringIntoParts($name,$value);
        $returned = $this->sanitiseVariableName($returned[0],$returned[1]);
        $returned = $this->sanitiseVariableValue($returned[0],$returned[1]);

        return [$returned[0],$returned[1]];
    }

    protected function readLinesFromFile($filePath) {
        $fileContent = file_get_contents($filePath);
        return explode(PHP_EOL,$fileContent);
    }

    protected function isComment($line) {
        $line = ltrim($line);
        return isset($line[0]) && $line[0] === '#';
    }

    protected function looksLikeSetter($line) {
        return strpos($line,'=') !== false;
    }

    protected function splitCompoundStringIntoParts($name,$value) {
        if (strpos($name,'=') !== false) {
            $returned = array_map('trim',explode('=',$name,2));
            $name = $returned[0];
            $value = $returned[1];
        }

        return [$name,$value];
    }

    /**
     * Strips quotes from the environment variable value.
     *
     * @param string $name
     * @param string $value
     *
     * @throws InvalidFileException
     * @return array
     */
    protected function sanitiseVariableValue($name,$value) {
        $value = trim($value);
        if (!$value) {
            return [$name,$value];
        }

        if ($this->beginsWithAQuote($value)) { // value starts with a quote
            $quote        = $value[0];
            $regexPattern = sprintf('/^
                %1$s          # match a quote at the start of the value
                (             # capturing sub-pattern used
                 (?:          # we do not need to capture this
                  [^%1$s\\\\] # any character other than a quote or backslash
                  |\\\\\\\\   # or two backslashes together
                  |\\\\%1$s   # or an escaped quote e.g \"
                 )*           # as many characters that match the previous rules
                )             # end of the capturing sub-pattern
                %1$s          # and the closing quote
                .*$           # and discard any string after the closing quote
                /mx',$quote);
            $value        = preg_replace($regexPattern,'$1',$value);
            $value        = str_replace(["\\$quote",'\\\\'],array($quote,'\\'),$value);
        } else {
            $parts = explode(' #',$value,2);
            $value = trim($parts[0]);

            // Unquoted values cannot contain whitespace
            if (preg_match('/\s+/',$value) > 0) {
                throw new InvalidFileException('DotEnv values containing spaces must be surrounded by quotes.');
            }
        }
        return [$name,trim($value)];
    }

    public function makePathRealPath($name,$value) {
        if (substr($name,-5) === '.path') {
            if (file_exists(realpath($value)) && is_dir(realpath($value))) {
                $value = realpath($value);
            }
            $value = str_replace(['/','\\'],DIRECTORY_SEPARATOR,$value);
        }
        return [$name,$value];
    }

    /**
     * Resolve the nested variables.
     * Look for ${varname} patterns in the variable value and replace with an
     * existing environment variable.
     *
     * @param string $value
     *
     * @return mixed
     */
    protected function resolveNestedVariables($value) {
        if (strpos($value,'$') !== false) {
            $loader = $this;
            $value  = preg_replace_callback('/\${([a-zA-Z0-9_.]+)}/',function($matchedPatterns) use ($loader) {
                $nestedVariable = $loader->getEnvironmentVariable($matchedPatterns[1]);
                if ($nestedVariable === null) {
                    return $matchedPatterns[0];
                }
                return $nestedVariable;
            },$value);
        }
        return $value;
    }

    protected function sanitiseVariableName($name,$value) {
        $name = trim(str_replace(['export ','\'','"'],'',$name));
        return [$name,$value];
    }

    protected function beginsWithAQuote($value) {
        return isset($value[0]) && ($value[0] === '"' || $value[0] === '\'');
    }

    public function getEnvironmentVariable($name) {
        switch(true) {
            case array_key_exists($name,$_ENV):
                return $_ENV[$name];
            case array_key_exists($name,$_SERVER):
                return $_SERVER[$name];
            default:
                $value = getenv($name);
                return $value === false ? null : $value; // switch getenv default to null
        }
    }

    public function setEnvironmentVariable($name,$value = null) {
        $returned = $this->normaliseEnvironmentVariable($name,$value);
        $name = $returned[0];
        $value = $returned[1];
        /* Don't overwrite existing environment variables if we're immutable
         * Ruby's dotenv does this with `ENV[key] ||= value`. */
        if ($this->immutable && $this->getEnvironmentVariable($name) !== null) {
            return;
        }
        /* If PHP is running as an Apache module and an existing
         * Apache environment variable exists, overwrite it */
        if (function_exists('apache_getenv') && function_exists('apache_setenv') && apache_getenv($name)) {
            apache_setenv($name,$value);
        }
        if (function_exists('putenv')) {
            putenv("$name=$value");
        }
        $_ENV[$name]    = $value;
        $_SERVER[$name] = $value;
        // Heepp::data('env.'.$name,$value);
    }

    public function clearEnvironmentVariable($name) {
        // Don't clear anything if we're immutable.
        if ($this->immutable) {
            return;
        }
        if (function_exists('putenv')) {
            putenv($name);
        }
        unset($_ENV[$name],$_SERVER[$name]);
    }
}
