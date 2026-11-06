<?xml version="1.0" encoding="UTF-8"?>
<fragment>
    <pml name="Preprocessed markup language"
          description="Defines the root of an PML document"
          html5="true"
          extends="core">
          
        <attributes description="Attributes that can be used on any pml node (element)">
            <accesskey
                dataType="string"
                description="Specifies a shortcut key to activate/focus an element"/>
            <class
                dataType="entities"
                description="Specifies one or more classnames for an element (refers to a class in a style sheet)"/>
            <contenteditable
                dataType="boolean"
                description="Specifies whether the content of an element is editable or not"/>
            <contextmenu
                dataType="html"
                description="Specifies a context menu for an element. The context menu appears when a user right-clicks on the element"/>
            <data-
                dataType="string"
                description="Used to store custom data private to the page or application"/>
            <dir
                dataType="token"
                description="Specifies the text direction for the content in an element"/>
            <draggable
                dataType="boolean"
                description="Specifies whether an element is draggable or not"/>
            <dropzone
                dataType="token"
                description="Specifies whether the dragged data is copied, moved, or linked, when dropped"/>
            <hidden
                dataType="boolean"
                description="Specifies that an element is not yet, or is no longer, relevant"/>
            <id
                dataType="normalizedString"
                description="Specifies a unique id for an element"/>
            <lang
                dataType="token"
                dataSrc="dataTypes/data/languages.xml"
                description="Specifies the language of the element's content"/>
            <spellcheck
                dataType="boolean"
                description="Specifies whether the element is to have its spelling and grammar checked or not"/>
            <style
                dataType="normalizedString"
                description="Specifies an inline CSS style for an element"/>
            <tabindex
                dataType="number"
                description="Specifies the tabbing order of an element"/>
            <title
                dataType="normalizedString"
                description="Specifies extra information about an element"/>
            <translate
                dataType="boolean"
                description="Specifies whether the content of an element should be translated or not"/>
        </attributes>
    </pml>
    
    <a name="Hyperlink"
       description="The 'a' tag defines a hyperlink, which is used to link from one page to another."
       html5="true"
       extends="pml">

        <attributes description="'a' Tag Attribute Reference">
            <href 
                dataType="string"
                description="Specifies the URL of the page the link goes to"/>
            <download 
                dataType="string" 
                tip="Should be a filename (the browser will automatically detect the correct file extension)" 
                document="Specifies that the target will be downloaded when a user clicks on the hyperlink. This attribute is only used if the href attribute is set. The value of the attribute will be the name of the downloaded file. There are no restrictions on allowed values, and the browser will automatically detect the correct file extension and add it to the file (.img, .pdf, .txt, .html, etc.)." />

            <hreflang
                dataType="language_code"
                dataSrc="dataTypes/data/languages.xml"
                description="Specifies the language of the linked document"/>
            </hreflang>

            <media 
                dataType="media_query" 
                description="Specifies what media/device the linked document is optimized for"/>
            </media>

            <rel
                dataType="enum"
                value="['alternate','author','bookmark','help','license','next','nofollow','noreferrer','prefetch','prev','search']"
                description="Specifies the relationship between the current document and the linked document">
            </rel>

            <target 
                dataType="enum"
                value="['_blank','_parent','_self','_top','framename']" 
                description="Specifies where to open the linked document">
            </target>

            <type 
                dataType="media_type"
                dataSrc="dataTypes/data/mediaTypes.xml" 
                description="Specifies the media type of the linked document">
            </type>
        </attributes>
    </a>
    
    <abbr name="Abbreviation"
        description="Defines an abbreviation or an acronym"
        html5="true" 
        extends="pml">
        
        <attributes description="'abbr' Tag Attribute Reference">
            
        </attributes>
    </abbr>
    
    <acronym name="Acronym"
        description="Not supported in HTML5. Use 'abbr' instead.Defines an acronym"
        html5="false"
        extends="pml">
        
        <attributes description="'acronym' Tag Attribute Reference">
            
        </attributes>
    </acronym>
    
    <address name="Address"
        description="Defines contact information for the author-owner of a document"
        html5="true"
        extends="pml">
        
        <attributes description="'address' Tag Attribute Reference">
            
        </attributes>
    </address>
    
    <applet name="Applet"
        description="Not supported in HTML5. Use 'embed' or 'object' instead. Defines an embedded applet"
        html5="false"
        extends="pml">
        
        <attributes description="'applet' Tag Attribute Reference">
            
        </attributes>
    </applet>
    
    <area name="Area"
        description="Defines an area inside an image-map"
        html5="true"
        extends="pml">
        
        <attributes description="'area' Tag Attribute Reference">
            <download
                dataType=""
                description=""/>
                
            <alt
                dataType=""
                description=""/>
                
            <coords
                dataType=""
                description=""/>
        </attributes>
    </area>
    
    <article name="Article"
        description="Defines an article"
        html5="true"
        extends="pml">
        
        <attributes description="'article' Tag Attribute Reference">
            
        </attributes>
    </article>  
       
    <aside name="Aside"
        description="Defines content aside from the page content"
        html5="true"
        extends="pml">
        
        <attributes description="'aside' Tag Attribute Reference">
            
        </attributes>
    </aside>
    
    <audio name="Audio"
        description="Defines sound content"
        html5="true"
        extends="pml">
        
        <attributes description="'audio' Tag Attribute Reference">
            <autoplay
                dataType=""
                description=""/>
                
            <controls
                dataType=""
                description=""/>
        </attributes>
    </audio>
    
    <b name="Bold"
        description="Defines bold text"
        html5="true"
        extends="html">
        
        <attributes description="'b' Tag Attribute Reference">
            
        </attributes>
    </b>
    
    <base name="Document Base URL"
        description="Specifies the base URL/target for all relative URLs in a document"
        html5="true"
        extends="html">
        
        <attributes description="'base' Tag Attribute Reference">
            
        </attributes>
    </base>
    
    <basefont name="Base Font"
        description="Not supported in HTML5. Use CSS instead. Specifies a default color, size, and font for all text in a document"
        html5="false"
        extends="html">
        
        <attributes description="'basefont' Tag Attribute Reference">
            
        </attributes>
    </basefont>
    
    <bdi name="Isolate Direction"
        description="Isolates a part of text that might be formatted in a different direction from other text outside it"
        html5="true"
        extends="html">
        
        <attributes description="'bdi' Tag Attribute Reference">
            
        </attributes>
    </bdi>
    
    <bdo name="Override Direction"
        description="Overrides the current text direction"
        html5="true"
        extends="html">
        
        <attributes description="'abbr' Tag Attribute Reference">
            
        </attributes>
    </bdo>
    
    <big name="Bigger Text"
        description="Not supported in HTML5. Use CSS instead. Defines big text"
        html5="false"
        extends="html">
        
        <attributes description="'big' Tag Attribute Reference">
            
        </attributes>
    </big>
    
    <blockquote name="Block Quote"
        description="Defines a section that is quoted from another source"
        html5="true"
        extends="html">
        
        <attributes description="'blockquote' Tag Attribute Reference">
            <cite
                dataType=""
                description=""/>
        </attributes>
    </blockquote>
    
    <body name="Document Body"
        description="Defines the document's body"
        html5="true"
        extends="html">
        
        <attributes description="'body' Tag Attribute Reference">
            
        </attributes>
    </body>
    
    <br name="Line Break"
        description="Defines a single line break"
        html5="true" 
        extends="html">
        
        <attributes description="'br' Tag Attribute Reference">
            
        </attributes>
    </br>
    
    <button name="Button"
        description="Defines a clickable button"
        html5="true"
        extends="html">
        
        <attributes description="'button' Tag Attribute Reference">
            <autofocus
                dataType=""
                description=""/>
            
            <disabled
                dataType=""
                description=""/>
                
            <type
                dataType=""
                description=""/>
        </attributes>
    </button>
    
    <canvas name="Canvas"
        description="Used to draw graphics, on the fly, via scripting (usually JavaScript)"
        html5="true"
        extends="html">
        
        <attributes description="'canvas' Tag Attribute Reference">
            
        </attributes>
    </canvas>
    
    <caption name="Table Caption"
        description="Defines a table caption"
        html5="true"
        extends="html">
        
        <attributes description="'caption' Tag Attribute Reference">
            
        </attributes>
    </caption>
    
    <center name="Center"
        description="Not supported in HTML5. Use CSS instead. Defines centered text"
        html5="false"
        extends="html">
        
        <attributes description="'center' Tag Attribute Reference">
            
        </attributes>
    </center>
    
    <cite name="Work Title"
        description="Defines the title of a work"
        html5="true"
        extends="html">
        
        <attributes description="'cite' Tag Attribute Reference">
            
        </attributes>
    </cite>
    
    <code name="Computer Code"
        description="Defines a piece of computer code"
        html5="true"
        extends="html">
        
        <attributes description="'code' Tag Attribute Reference">
            
        </attributes>
    </code>
    
    <col name="Column"
        description="Specifies column properties for each column within a 'colgroup' element"
        html5="true"
        extends="html">
        
        <attributes description="'col' Tag Attribute Reference">
            
        </attributes>
    </col>
    
    <colgroup name="Column Group"
        description="Specifies a group of one or more columns in a table for formatting"
        html5="true" 
        extends="html">
        
        <attributes description="'colgroup' Tag Attribute Reference">
            
        </attributes>
    </colgroup>
    
    <datalist name="Data List"
        description="Specifies a list of pre-defined options for input controls"
        html5="true"
        extends="html">
        
        <attributes description="'datalist' Tag Attribute Reference">
            
        </attributes>
    </datalist>
    
    <dd name="Description"
        description="Defines a description/value of a term in a description list"
        html5="true"
        extends="html">
        
        <attributes description="'dd' Tag Attribute Reference">
            
        </attributes>
    </dd>    
    
    <del name="Deleted Text"
        description="Defines text that has been deleted from a document"
        html5="true"
        extends="html">
        
        <attributes description="'del' Tag Attribute Reference">
            <cite
                dataType=""
                description=""/>
                
            <datetime
                dataType=""
                description=""/>
        </attributes>
    </del>
    
    <details name="Details"
        description="Defines additional details that the user can view or hide"
        html5="true"
        extends="html">
        
        <attributes description="'details' Tag Attribute Reference">
            
        </attributes>
    </details>
    
    <dfn name="Define Instance of Term"
        description="Represents the defining instance of a term"
        html5="true"
        extends="html">
        
        
        <attributes description="'dfn' Tag Attribute Reference">
            
        </attributes>
    </dfn>
    
    <dialog name="Dialog Box"
        description="Defines a dialog box or window"
        html5="true"
        extends="html">
        
        <attributes description="'dialog' Tag Attribute Reference">
            
        </attributes>
    </dialog>
    
    <dir name="Directory List"
        description="Not supported in HTML5. Use 'ul' instead Defines a directory list"
        html5="false"
        extends="html">
        
        
        <attributes description="'dir' Tag Attribute Reference">
            
        </attributes>
    </dir>
    
    <div name="Divides Document Section"
        description="Defines a section in a document"
        html5="true"
        extends="html">
        
        
        <attributes description="'div' Tag Attribute Reference">
            
        </attributes>
    </div>
    
    <dl name="Description List"
        description="Defines a description list"
        html5="true"
        extends="html">
        
        <attributes description="'dl' Tag Attribute Reference">
            
        </attributes>
    </dl>
    
    <dt name="Define Term"
        description="Defines a term-name in a description list"
        html5="true"
        extends="html">
        
        <attributes description="'dt' Tag Attribute Reference">
            
        </attributes>
    </dt>
    
    <em name="Emphasize"
        description="Defines emphasized text"
        html5="true"
        extends="html">
        
        <attributes description="'em' Tag Attribute Reference">
            
        </attributes>
    </em>
    
    <embed name="Embed"
        description="Defines a container for an external (non-HTML) application"
        html5="true"
        extends="html">
        
        <attributes description="'embed' Tag Attribute Reference">
            
        </attributes>
    </embed>
    
    <fieldset name="Fieldset"
        description="Groups related elements in a form"
        html5="true"
        extends="html">
        
        <attributes description="'fieldset' Tag Attribute Reference">
            <disabled
                dataType=""
                description=""/>
        </attributes>
    </fieldset>
    
    <figcaption name="Figure Caption"
        description="Defines a caption for a 'figure' element"
        html5="true" 
        extends="html">
        
        <attributes description="'figcaption' Tag Attribute Reference">
            
        </attributes>
    </figcaption>
    
    <figure name="Figure"
        description="Specifies self-contained content"
        html5="true"
        extends="html">
        
        <attributes description="'figure' Tag Attribute Reference">
            
        </attributes>
    </figure>
    
    <font name="Font"
        description="Not supported in HTML5. Use CSS instead. Defines font, color, and size for text"
        html5="false"
        extends="html">
        
        <attributes description="'font' Tag Attribute Reference">
            
        </attributes>
    </font>
    
    <footer name="Footer"
        description="Defines a footer for a document or section"
        html5="true"
        extends="html">
        
        <attributes description="'footer' Tag Attribute Reference">
            
        </attributes>
    </footer>
    
    <form name="Form"
        description="Defines an HTML form for user input"
        html5="true"
        extends="html">
        
        <attributes description="'form' Tag Attribute Reference">
            <accept-charset
                dataType=""
                description=""/>
                
            <action
                dataType=""
                description=""/>
                
            <autocomplete
                dataType=""
                description=""/>
        </attributes>
    </form>
    
    <frame name="Frame"
        description="Not supported in HTML5. Defines a window (a frame) in a frameset"
        html5="false"
        extends="html">
        
        <attributes description="'frame' Tag Attribute Reference">
            
        </attributes>
    </frame>
    
    <frameset name="Frame Set"
        description="Not supported in HTML5. Defines a set of frames"
        html5="true"
        extends="html">
        
        <attributes description="'frameset' Tag Attribute Reference">
            
        </attributes>
    </frameset>
    
    <h1 name="Heading 1"
        description="Defines HTML heading (size:1) 1: large 6:small"
        html5="true" 
        extends="html">
        
        <attributes description="'h1' Tag Attribute Reference">
            
        </attributes>
    </h1>
    
    <h2 name="Heading 2"
        description="Defines HTML heading (size:2) 1: large 6:small"
        html5="true" 
        extends="html">
        
        <attributes description="'h2' Tag Attribute Reference">
            
        </attributes>
    </h2>
    
    <h3 name="Heading 3"
        description="Defines HTML heading (size:3) 1: large 6:small"
        html5="true"
        extends="html">
        
        <attributes description="'h3' Tag Attribute Reference">
            
        </attributes>
    </h3>
    
    <h4 name="Heading 4"
        description="Defines HTML heading (size:4) 1: large 6:small"
        html5="true" 
        extends="html">
        
        <attributes description="'h4' Tag Attribute Reference">
            
        </attributes>
    </h4>
    
    <h5 name="Heading 5"
        description="Defines HTML heading (size:5) 1: large 6:small"
        html5="true" 
        extends="html">
        
        <attributes description="'h5' Tag Attribute Reference">
            
        </attributes>
    </h5>
    
    <h6 name="Heading 6"
        description="Defines HTML heading (size:6) 1: large 6:small"
        html5="true" 
        extends="html">
        
        <attributes description="'h6' Tag Attribute Reference">
            
        </attributes>
    </h6>
    
    <head name="Document head"
        description="Defines information about the document"
        html5="true" 
        extends="html">
        
        <attributes description="'head' Tag Attribute Reference">
            
        </attributes>
    </head>
    
    <header name="Header"
        description="Defines a header for a document or section"
        html5="true" 
        extends="html">
        
        <attributes description="'header' Tag Attribute Reference">
            
        </attributes>
    </header>
    
    <hr name="Horizontal Rule"
        description="Defines a thematic change in the content"
        html5="true" 
        extends="html">
        
        <attributes description="'hr' Tag Attribute Reference">
            
        </attributes>
    </hr>
    
    <i name="Important or Italic"
        description="Defines a part of text in an alternate voice or mood"
        html5="true"
        extends="html">
        
        <attributes description="'i' Tag Attribute Reference">
            
        </attributes>
    </i>
    
    <iframe name="Inline Frame"
        description="Defines an in-line frame"
        html5="true" 
        extends="html">
        
        <attributes description="'iframe' Tag Attribute Reference">
            
        </attributes>
    </iframe>
    
    <img name="Image"
        description="Defines an image"
        html5="true"
        extends="html">
        
        <attributes description="'img' Tag Attribute Reference">
            <alt
                dataType=""
                description=""/>
                
            <src
                dataType=""
                description=""/>
        </attributes>
    </img>
    
    <input name="Input (Textfield)"
        description="Defines an input control"
        html5="true" 
        extends="html">
        
        <attributes description="'input' Tag Attribute Reference">
            <accept
                dataType=""
                description=""/>
                
            <alt
                dataType=""
                description=""/>
                
            <autocomplete
                dataType=""
                description=""/>
                
            <autofocus
                dataType=""
                description=""/>
                
            <checked
                dataType=""
                description=""/>
                
            <dirname
                dataType=""
                description=""/>
        </attributes>
    </input>
    
    <ins name="Inserted Text"
        description="Defines a text that has been inserted into a document"
        html5="true" 
        extends="html">
        
        <attributes description="'ins' Tag Attribute Reference">
            <cite
                dataType=""
                description=""/>
                
            <datetime
                dataType=""
                description=""/>
        </attributes>
    </ins>
    
    <kbd name="Keyboard Input"
        description="Defines keyboard input"
        html5="true"
        extends="html">
        
        <attributes description="'kbd' Tag Attribute Reference">
            
        </attributes>
    </kbd>
        
    <keygen name="Keypair Generator"
        description="Defines a key-pair generator field (for forms)"
        html5="true"
        extends="html">
        
        <attributes description="'keygen' Tag Attribute Reference">
            <autofocus
                dataType=""
                description=""/>
                
            <challenge
                dataType=""
                description=""/>
        </attributes>
    </keygen>
    
    <label name="Input Label"
        description="Defines a label for an input element"
        html5="true"
        extends="html">
        
        <attributes description="'label' Tag Attribute Reference">
            
        </attributes>
    </label>
    
    <legend name="Fieldset Caption"
        description="Defines a caption for a fieldset element"
        html5="true"
        extends="html">
        
        <attributes description="'legend' Tag Attribute Reference">
            
        </attributes>
    </legend>
    
    <li name="List Item"
        description="Defines a list item"
        html5="true" 
        extends="html">
        
        <attributes description="'li' Tag Attribute Reference">
            
        </attributes>
    </li>
    
    <link name="Relational Link"
        description="Defines the relationship between a document and an external resource (most used to link to style sheets)"
        html5="true"
        extends="html">
        
        <attributes description="'link' Tag Attribute Reference">
            
        </attributes>
    </link>
    
    <main name="Main Content"
        description="Specifies the main content of a document"
        html5="true"
        extends="html">
        
        <attributes description="'main' Tag Attribute Reference">
            
        </attributes>
    </main>
    
    <map name="Image Map"
        description="Defines a client-side image-map"
        html5="true"
        extends="html">
        
        <attributes description="'map' Tag Attribute Reference">
            
        </attributes>
    </map>
    
    <mark name="Marked Text"
        description="Defines marked/highlighted text"
        html5="true"
        extends="html">
        
        <attributes description="'mark' Tag Attribute Reference">
            
        </attributes>
    </mark>
    
    <menu name="Command Menu"
        description="Defines a list-menu of commands"
        html5="true"
        extends="html">
        
        <attributes description="'menu' Tag Attribute Reference">
            
        </attributes>
    </menu>
    
    <menuitem name="Command Menu Item"
        description="Defines a command-menu item that the user can invoke from a popup menu"
        html5="true" 
        extends="html">
        
        <attributes description="'menuitem' Tag Attribute Reference">
            
        </attributes>
    </menuitem>
    
    <meta name="Meta Data"
        description="Defines meta-data about an HTML document"
        html5="true"
        extends="html">
        
        <attributes description="'meta' Tag Attribute Reference">
            <charset
                dataType=""
                description=""/>
                
            <content
                dataType=""
                description=""/>
        </attributes>
    </meta>
    
    <meter name="Scalar Measurement"
        description="Defines a scalar measurement within a known range (a gauge)"
        html5="true"
        extends="html">
        
        <attributes description="'meter' Tag Attribute Reference">
            
        </attributes>
    </meter>
    
    <nav name="Navigation"
        description="Defines navigation links"
        html5="true"
        extends="html">
        
        <attributes description="'nav' Tag Attribute Reference">
            
        </attributes>
    </nav>
    
    <noframesname name="Frame alternate Content"
        description="Not supported in HTML5 Defines an alternate content for users that do not support frames"
        html5="false"
        extends="html">
        
        <attributes description="'noframesname' Tag Attribute Reference">
            
        </attributes>
    </noframesname>
    
    <noscriptname name="Script Alternate"
        description="Defines an alternate content for users that do not support client-side scripts"
        html5="true"
        extends="html">
        
        <attributes description="'noscriptname' Tag Attribute Reference">
            
        </attributes>
    </noscriptname>
    
    <object name="Object"
        description="Defines an embedded object"
        html5="true"
        extends="html">
        
        <attributes description="'object' Tag Attribute Reference">
            <data
                dataType=""
                description=""/>
        </attributes>
    </object>
    
    <ol name="Ordered List"
        description="Defines an ordered list"
        html5="true"
        extends="html">
        
        <attributes description="'ol' Tag Attribute Reference">
            
        </attributes>
    </ol>
    
    <optgroup name="Options Group"
        description="Defines a group of related options in a drop-down list"
        html5="true"
        extends="html">
        
        <attributes description="'optgroup' Tag Attribute Reference">
            <disabled
                dataType=""
                description=""/>
        </attributes>
    </optgroup>
    
    <option name="Option"
        description="Defines an option in a drop-down list"
        html5="true"
        extends="html">
        
        <attributes description="'option' Tag Attribute Reference">
            disabled
        </attributes>
    </option>
    
    <output name="Calc Result"
        description="Defines the result of a calculation"
        html5="true"
        extends="html">
        
        <attributes description="'output' Tag Attribute Reference">
            
        </attributes>
    </output>
    
    <p name="Paragraph"
        description="Defines a paragraph"
        html5="true"
        extends="html">
        
        <attributes description="'p' Tag Attribute Reference">
            
        </attributes>
    </p>
    
    <param name="Object Parameter"
        description="Defines a parameter for an object"
        html5="true"
        extends="html">
        
        <attributes description="'param' Tag Attribute Reference">
            
        </attributes>
    </param>
    
    <pre name="Pre-Formatted Text"
        description="Defines pre-formatted text"
        html5="true"
        extends="html">
        
        <attributes description="'pre' Tag Attribute Reference">
            
        </attributes>
    </pre>
    
    <progress name="Progress"
        description="Represents the progress of a task"
        html5="true"
        extends="html">
        
        <attributes description="'progress' Tag Attribute Reference">
            
        </attributes>
    </progress>
    
    <q name="Quotation"
        description="Defines a short quotation"
        html5="true"
        extends="html">
        
        <attributes description="'q' Tag Attribute Reference">
            <cite
                dataType=""
                description=""/>
        </attributes>
    </q>
    
    <rp name="Ruby Annotations Alternate"
        description="Defines what to show in browsers that do not support ruby annotations"
        html5="true"
        extends="html">
        
        <attributes description="'rp' Tag Attribute Reference">
            
        </attributes>
    </rp>
    
    <rt name="Pronunciation Explanation"
        description="Defines an explanation-pronunciation of characters (for East Asian typography)"
        html5="true"
        extends="html">
        
        <attributes description="'rt' Tag Attribute Reference">
            
        </attributes>
    </rt>
    
    <ruby name="Ruby Annotation"
        description="Defines a ruby annotation (for East Asian typography)"
        html5="true"
        extends="html">
        
        <attributes description="'ruby' Tag Attribute Reference">
            
        </attributes>
    </ruby>
    
    <s name="Incorrect Text"
        description="Defines text that is no longer correct"
        html5="true"
        extends="html">
        
        <attributes description="'s' Tag Attribute Reference">
            
        </attributes>
    </s>
    
    <samp name="Sample Output"
        description="Defines sample output from a computer program"
        html5="true"
        extends="html">
        
        <attributes description="'samp' Tag Attribute Reference">
            
        </attributes>
    </samp>
    
    <script name="Script (Client Side)"
        description="Defines a client side script"
        html5="true"
        extends="html">
        
        <attributes description="'script' Tag Attribute Reference">
            <async
                dataType=""
                description=""/> 
            
            <defer
                dataType=""
                description=""/> 
            
            <charset
                dataType=""
                description=""/>
        </attributes>
    </script>
    
    <section name="Document Section"
        description="Defines a section in a document"
        html5="true"
        extends="html">
        
        <attributes description="'section' Tag Attribute Reference">
            
        </attributes>
    </section>
    
    <select name="Select List"
        description="Defines a drop-down list"
        html5="true"
        extends="html">
        
        <attributes description="'select' Tag Attribute Reference">
            <autofocus
                dataType=""
                description=""/>
            
            <disabled
                dataType=""
                description=""/>
        </attributes>
    </select>
    
    <small name="Smaller Text"
        description="Defines smaller text"
        html5="true"
        extends="html">
        
        <attributes description="'small' Tag Attribute Reference">
            
        </attributes>
    </small>
    
    <source name="Source"
        description="Defines multiple media resources for media elements ('video ' and 'audi ')"
        html5="true"
        extends="html">
        
        <attributes description="'source' Tag Attribute Reference">
            
        </attributes>
    </source>
    
    <span name="Section or Segment" 
        escription="Defines a section in a document"
        html5="true"
        extends="html">
        
        <attributes description="'span' Tag Attribute Reference">
            
        </attributes>
    </span>
    
    <strike name="Strikethrough Text"
        description="Not supported in HTML5. Use 'del' or 's' instead. Defines strikethrough text"
        html5="false"
        extends="html">
        
        <attributes description="'strike' Tag Attribute Reference">
            
        </attributes>
    </strike>
    
    <strong name="Bold and Important Text" 
        description="Defines important text"
        html5="true"
        extends="html">
        
        <attributes description="'strong' Tag Attribute Reference">
            
        </attributes>
    </strong>
    
    <style name="CSS Style Info" 
        description="Defines style information for a document"
        html5="true"
        extends="html">
        
        <attributes description="'style' Tag Attribute Reference">
            
        </attributes>
    </style>
    
    <sub name="Subscripted Text" 
        description="Defines subscripted text"
        html5="true"
        extends="html">
        
        <attributes description="'sub' Tag Attribute Reference">
            
        </attributes>
    </sub>
    
    <summary name="Summary" 
        description="Defines a visible heading for a 'details' element"
        html5="true"
        extends="html">
        
        <attributes description="'summary' Tag Attribute Reference">
            
        </attributes>
    </summary>
    
    <sup name="Superscripted Text" 
        description="Defines superscripted text"
        html5="true"
        extends="html">
        
        <attributes description="'sup' Tag Attribute Reference">
            
        </attributes>
    </sup>
    
    <table name="Table" 
        description="Defines a table"
        html5="true"
        extends="html">
        
        <attributes description="'table' Tag Attribute Reference">
            
        </attributes>
    </table>
    
    <tbody name="Table Body" 
        description="Groups the body content in a table"
        html5="true"
        extends="html">
        
        <attributes description="'tbody' Tag Attribute Reference">
            
        </attributes>
    </tbody>
    
    <td name="Table Data" 
        description="Defines a cell in a table"
        html5="true"
        extends="html">
        
        <attributes description="'td' Tag Attribute Reference">
            <colspan
                dataType=""
                description=""/>
        </attributes>
    </td>
    
    <textarea name="Text Area"
        description="Defines a multi-line input control (text area)"
        html5="true"
        extends="html">
        
        <attributes description="'textarea' Tag Attribute Reference">
            <autofocus
                dataType=""
                description=""/>
            
            <cols
                dataType=""
                description=""/>
                
            <dirname
                dataType=""
                description=""/>
        </attributes>
    </textarea>
    
    <tfoot name="Table Footer"
        description="Groups the footer content in a table"
        html5="true"
        extends="html">
        
        <attributes description="'tfoot' Tag Attribute Reference">
            
        </attributes>
    </tfoot>
    
    <th name="Table heading"
        description="Defines a header cell in a table"
        html5="true"
        extends="html">
        
        <attributes description="'th' Tag Attribute Reference">
            <colspan
                dataType=""
                description=""/>
        </attributes>
    </th>
    
    <thead name="Table Header"
        description="Groups the header content in a table"
        html5="true" extends="html">
        
        <attributes description="'thead' Tag Attribute Reference">
            
        </attributes>
    </thead>
    
    <time name="Time"
        description="Defines a date/time"
        html5="true"
        extends="html">
        
        <attributes description="'time' Tag Attribute Reference">
            <datetime
                dataType=""
                description=""/>
        </attributes>
    </time>
    
    <title name="Title"
        description="Defines a title for the document"
        html5="true"
        extends="html">
        
        <attributes description="'title' Tag Attribute Reference">
            
        </attributes>
    </title>
    
    <tr name="Table Row"
        description="Defines a row in a table"
        html5="true"
        extends="html">
        
        <attributes description="'tr' Tag Attribute Reference">
            
        </attributes>
    </tr>
    
    <track name="Video/Audio Track"
        description="Defines text tracks for media elements 'video' and 'audio'"
        html5="true"
        extends="html">
        
        <attributes description="'track' Tag Attribute Reference">
            <default
                dataType=""
                description=""/>
        </attributes>
    </track>
    
    <u name="Different Styled Text"
        description="Defines text that should be stylistically different from normaltext"
        html5="true"
        extends="html">
        
        <attributes description="'u' Tag Attribute Reference">
            
        </attributes>
    </u>
    
    <ul name="Unordered List"
        description="Defines an unordered list"
        html5="true"
        extends="html">
        
        <attributes description="'ul' Tag Attribute Reference">
            
        </attributes>
    </ul>
    
    
    <var name="Variable"
        description="Defines a variable"
        html5="true"
        extends="html">
        
        <attributes description="'v' Tag Attribute Reference">
            
        </attributes>
    </var>
    
    
    <video name="Video"
        description="Defines a video or movie"
        html5="true"
        extends="html">
        
        <attributes description="'video' Tag Attribute Reference">
            <autoplay
                dataType="enum"
                value="[]"
                description="Start playing the video as soon as the value has been satisfied"/>
            <controls
                dataType="boolean"
                description="Show Video Controls or Not"/>
        </attributes>
    </video>
    
    <wbr name="Possible Line Break"
        description="Defines a possible line-break"
        html5="true"
        extends="html">
        
        <attributes description="'wbr' Tag Attribute Reference">
            
        </attributes>
    </wbr>
</fragment>