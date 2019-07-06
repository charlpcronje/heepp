# CORE PHTML VIEW FILE

## Some use full tags

### LAZYLOAD:

- SCSS:
```
    <lazyload
        path=""
        url=""
        file=""/>
```

- CSS
```
    <css
        src="//assets/css/dev.scss"/>
```

- JS
```
    <lazyload
        src="views/console/js/dev.js"
        instantiate="dev"
        extend="console"
        callback="core.console.dev.init"
        context="window"/>
```

## ENV SETTINGS AND PATHS

-BASE
```
    document.root=${base.path}
    domain=${http.host}
    base.path=${CONTEXT_DOCUMENT_ROOT}/
    core.path=${base.path}core/
```

-REQUEST
```
    request.scheme=${REQUEST_SCHEME}
    request.url=${request.scheme}://${http.host}${context.prefix}
    request.method=${REQUEST_METHOD}
    request.uri=${REQUEST_URI}
```

-PROJECT
```
    project.ns=${projects.${project.name}.path}
    project.path=${document.root}/${project.ns}/
    project.assets.path=${project.path}assets/
    project.config.path=${project.path}config/
    project.connections.path=${project.config.path}database/
    project.controllers.path=${project.path}controllers/
    project.elements.path=${project.path}elements/
    project.storage.path=${project.path}storage/
    project.models.path=${project.path}models/
    project.routes.path=${project.path}routes/
    project.session.path=${project.path}sessions/
    project.upload.path=${project.path}uploads/
    project.views.path=${project.path}views/
```

-URLS - PROJECT
```
    project.url=${request.scheme}://${domain}/${project.ns}/
    project.controllers.url=${project.url}controllers/
    project.upload.url=${project.url}uploads/
    project.views.url=${project.url}views/
```

-PATHS - ELEMENT AND LIBRARY
```
    core.element.path=${core.path}element/
    core.library.path=${core.path}library/
```

-URLS - Http CORE ELEMENT AND LIBRARY URLS
```
    base.url=${request.scheme}://${domain}/
    core.url=${base.url}core/
    core.element.url=${core.url}element/
    core.library.url=${core.url}library/
````

-URLS - PROJECT ELEMENT AND LIBRARY
```
    project.assets.url=${project.url}assets/
```

-PATHS - CORE PATHS
```
    core.app.path=${core.path}app/
    core.app.config.path=${core.app.path}config/
    core.app.views.path=${core.app.path}views/
```

-URLS - CORE URLS
```
    core.app.url=${core.url}app/
```

-CORE VIEW EVENTS
```
    core.event.click.load="Controller/Method/Arg1/Arg2/.."
```

-CORE PHP SET AND GET DOT DATA
```
    $this->getData('dot.key',$default = null);
    $this->setData('dot.key',$data);
    $this->session('dot.key');
```
