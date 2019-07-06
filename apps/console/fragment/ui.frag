<?xml version="1.0" encoding="UTF-8"?>
<html>
    <ui name="User Interface Element"
        description="Defines a User Interface Element"
        html5="false"
        extends="html">
        
        <library:base section="jquery,phpjs,data,core"/>
        
        <attributes description="'ui' Tag attribute reference">
            
        </attributes>
    </ui>
    
    <init name="Init Eleent"
          description="This element loads and initiates scripts, then it deletes itself"
          tag="delete">  
        
        <attributes description="'init' Tag Attribute Reference">
            <data-selector
                dataType="string"
                description="jQuery Selector of Element to Initiate"/>
            
            <data-library
                dataType="string"
                description="Library to load before initiation"/>
            
            <data-library-section
                dataType="string"
                description="Library sections to load (Comma Seperated)"/> 
        </attributes>
        
        
        <library library="[data-library]" section="[data-library-section]"/>
    </init>
    
    <ui:init:mScrollbar name="mScrollbar Element"
                        description="Defines an mScrollbar Element that extends the init Element Loads the mScrollbar Library">
        
        <attributes description="'mScrollbar' Tag Attribute Reference">            
            <data-library
                valueDefault="ui"/>
            
            <data-library-section
                valueDefault="mScrollbar"/>
            
            <data-set-top
                dataType="number"
                description="Top Position of Scrollbar"
                valueDefault="0"/>
            
            <data-set-left
                dataType="number"
                description="Left Position of Scrollbar"
                valueDefault="0"/>
            
            <data-axis
                dataType="enum"
                description="Should there be Scrollbars Horizontal(x) or Vertical(y) or Both(xy)"
                valueOptions="['x','y','xy']"
                valueDefault="y"/>
            
            <data-scrollbar-position
                dataType="enum"
                description="Should the scrollbar 'inside' or 'outside' the container"
                valueOptions="['inside','outside']"
                valueDefault="inside"/>
            
            <data-scroll-inertia
                dataType="number"
                description="The Inertia when scrolling"
                valueDefault="950"/>
            
            <data-always-show-scrollbar
                dataType="boolean"
                description="Should the Scrollbar always be visible (1:Yes, 0:No)"
                valueDefault="0"/>
        </attributes>
    </ui:init:mScrollbar>
    <mScrollbar extends="ui:init:mScrollbar"/>
    
    <ajax name="Async JS and XML Elements"
          description="Elements that will be loading content from the server in a asyncronous manner to build more complex elements or to load some internal or external scripts or stylesheets"
          html5="false"
          extends="ui">
        
        <attributes description="'ajax' Tag attribute reference">
            <data-before-send
                dataType="string"
                description="Specify a javascript function to call when just before the ajax request is sent"/>
            
            <data-success
                dataType="string"
                description="specify a javascript function to call when the ajax request was successful"/>
            
            <data-complete
                dataType="string"
                description="Specify a javascript function to call when the ajax request reached 'complete' status"/>
            
            <data-error
                dataType="string"
                description="Specify a javascript function to call when ajax request created an 'Error'"/>
            
            <data-url
                dataType="anyURI"
                description="Enter a URL that should be called with the AJAX request"/>
            
            <data-method
                dataType="enum"
                valueOptions="['GET','POST']"
                valueDefault="POST"
                description="Specify the method in which the data must be sent to the server. Either 'post' or 'get'"/>
            
            <data-data-type
                dataType="enum"
                valueOptions="['json','html','script','text','xml','object','boolean']"
                valueDefault="json"
                description="Specify the data type you expect back in your ajax response"/>
            
            <data-async
                dataType="boolean"
                valueDefault="true"
                description="Should the ajax request be run syncronously or async. Specify either 'true' or 'false'"/>
            
            <data-data
                dataType="js_object"
                description="Specify a javascript object to send to the server"/>
        </attributes>
    </ajax>
    
    <ui:ajax:html:a name="User Interface Ajax Element Extended by HTML Hyperlink"
                    description="An Element that will auto include the libraries specified in all extended elements that will perform an ajax request, this element also contains the attributes of the HTML Hyperlink"
                    tag="a">
        
        <attributes description="'ui:ajax:html:a' Tag Attribute Reference">
            <data-url
                valueDefault="[href]"
                description="Enter a URL that should be called with the AJAX request. The default value has been set to the HTML Hyperlink's href attribute"/>
        </attributes>
        
        <bindEvents
            bindMethod="$.core.ui.bindEvent" 
            description="'ui:ajax:html:a' Tag Event Bindings">
            <click callback="event.preventDefault()"/>
        </bindEvents>
    </ui:ajax:html:a>
    <a.ajax extends="ui:ajax:html:a"/>
</html>
