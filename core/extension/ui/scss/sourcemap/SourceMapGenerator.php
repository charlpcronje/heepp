<?php
namespace core\extension\ui\scss\sourcemap;
use core\extension\ui\scss\exception\CompilerException;

class SourceMapGenerator {
    const VERSION = 3;
    protected $defaultOptions = array(
        'sourceRoot' => '',
        'sourceMapFilename' => null,
        'sourceMapURL' => null,
        'sourceMapWriteTo' => null,
        'outputSourceFiles' => false,
        'sourceMapRootpath' => '',
        'sourceMapBasepath' => ''
    );

    protected $encoder;
    protected $mappings = array();
    protected $contentsMap = array();
    protected $sources = array();
    protected $source_keys = array();
    private $options;
    public function __construct(array $options = []) {
        $this->options = array_merge($this->defaultOptions, $options);
        $this->encoder = new Base64VLQEncoder();
    }

    public function addMapping($generatedLine, $generatedColumn, $originalLine, $originalColumn, $sourceFile) {
        $this->mappings[] = array(
            'generated_line' => $generatedLine,
            'generated_column' => $generatedColumn,
            'original_line' => $originalLine,
            'original_column' => $originalColumn,
            'source_file' => $sourceFile
        );
        $this->sources[$sourceFile] = $sourceFile;
    }

    public function saveMap($content) {
        $file = $this->options['sourceMapWriteTo'];
        $dir  = dirname($file);
        // directory does not exist
        if (! is_dir($dir)) {
            // FIX ME: create the dir automatically?
            throw new CompilerException(sprintf('The directory "%s" does not exist. Cannot save the source map.', $dir));
        }
        // FIX ME: proper saving, with dir write check!
        if (file_put_contents($file, $content) === false) {
            throw new CompilerException(sprintf('Cannot save the source map to "%s"', $file));
        }
        return $this->options['sourceMapURL'];
    }

    public function generateJson() {
        $sourceMap = array();
        $mappings  = $this->generateMappings();
        // File version (always the first entry in the object) and must be a positive integer.
        $sourceMap['version'] = self::VERSION;
        // An optional name of the generated code that this source map is associated with.
        $file = $this->options['sourceMapFilename'];
        if ($file) {
            $sourceMap['file'] = $file;
        }
        // An optional source root, useful for relocating source files on a server or removing repeated values in the
        // 'sources' entry. This value is prepended to the individual entries in the 'source' field.
        $root = $this->options['sourceRoot'];
        if ($root) {
            $sourceMap['sourceRoot'] = $root;
        }
        // A list of original sources used by the 'mappings' entry.
        $sourceMap['sources'] = array();
        foreach ($this->sources as $source_uri => $source_filename) {
            $sourceMap['sources'][] = $this->normalizeFilename($source_filename);
        }
        // A list of symbol names used by the 'mappings' entry.
        $sourceMap['names'] = array();
        // A string with the encoded mapping data.
        $sourceMap['mappings'] = $mappings;
        if ($this->options['outputSourceFiles']) {
            // An optional list of source content, useful when the 'source' can't be hosted.
            // The contents are listed in the same order as the sources above.
            // 'null' may be used if some original sources should be retrieved by name.
            $sourceMap['sourcesContent'] = $this->getSourcesContent();
        }
        // less.js compat fixes
        if (count($sourceMap['sources']) && empty($sourceMap['sourceRoot'])) {
            unset($sourceMap['sourceRoot']);
        }
        return json_encode($sourceMap);
    }

    protected function getSourcesContent() {
        if (empty($this->sources)) {
            return null;
        }
        $content = array();
        foreach ($this->sources as $sourceFile) {
            $content[] = file_get_contents($sourceFile);
        }
        return $content;
    }

    public function generateMappings() {
        if (! count($this->mappings)) {
            return '';
        }
        $this->source_keys = array_flip(array_keys($this->sources));
        // group mappings by generated line number.
        $groupedMap = $groupedMapEncoded = array();
        foreach ($this->mappings as $m) {
            $groupedMap[$m['generated_line']][] = $m;
        }
        ksort($groupedMap);
        $lastGeneratedLine = $lastOriginalIndex = $lastOriginalLine = $lastOriginalColumn = 0;
        foreach ($groupedMap as $lineNumber => $line_map) {
            while (++$lastGeneratedLine < $lineNumber) {
                $groupedMapEncoded[] = ';';
            }
            $lineMapEncoded = array();
            $lastGeneratedColumn = 0;
            foreach ($line_map as $m) {
                $mapEncoded = $this->encoder->encode($m['generated_column'] - $lastGeneratedColumn);
                $lastGeneratedColumn = $m['generated_column'];
                // find the index
                if ($m['source_file']) {
                    $index = $this->findFileIndex($m['source_file']);
                    if ($index !== false) {
                        $mapEncoded .= $this->encoder->encode($index - $lastOriginalIndex);
                        $lastOriginalIndex = $index;
                        // lines are stored 0-based in SourceMap spec version 3
                        $mapEncoded .= $this->encoder->encode($m['original_line'] - 1 - $lastOriginalLine);
                        $lastOriginalLine = $m['original_line'] - 1;
                        $mapEncoded .= $this->encoder->encode($m['original_column'] - $lastOriginalColumn);
                        $lastOriginalColumn = $m['original_column'];
                    }
                }
                $lineMapEncoded[] = $mapEncoded;
            }
            $groupedMapEncoded[] = implode(',', $lineMapEncoded) . ';';
        }
        return rtrim(implode($groupedMapEncoded), ';');
    }

    protected function findFileIndex($filename) {
        return $this->source_keys[$filename];
    }

    protected function normalizeFilename($filename) {
        $filename = $this->fixWindowsPath($filename);
        $rootpath = $this->options['sourceMapRootpath'];
        $basePath = $this->options['sourceMapBasepath'];
        // "Trim" the 'sourceMapBasepath' from the output filename.
        if (strpos($filename, $basePath) === 0) {
            $filename = substr($filename, strlen($basePath));
        }
        // Remove extra leading path separators.
        if (strpos($filename, '\\') === 0 || strpos($filename, '/') === 0) {
            $filename = substr($filename, 1);
        }
        return $rootpath . $filename;
    }

    public function fixWindowsPath($path, $addEndSlash = false) {
        $slash = ($addEndSlash) ? '/' : '';
        if (! empty($path)) {
            $path = str_replace('\\', '/', $path);
            $path = rtrim($path, '/') . $slash;
        }
        return $path;
    }
}
