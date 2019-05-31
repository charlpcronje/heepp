# Website that generates Favicons
> This website will also generate your favicon manifests for mobile

*https://realfavicongenerator.net/*

### Example of generated code for html `<head>`

```
<link rel="apple-touch-icon" sizes="180x180" href="/assets/favicons/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="/assets/favicons/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/assets/favicons/favicon-16x16.png">
<link rel="manifest" href="/assets/favicons/site.webmanifest">
<link rel="mask-icon" href="/assets/favicons/safari-pinned-tab.svg" color="#f13a24">
<link rel="shortcut icon" href="/assets/favicons/favicon.ico">
<meta name="msapplication-TileColor" content="#da532c">
<meta name="msapplication-TileImage" content="/assets/favicons/mstile-144x144.png">
<meta name="msapplication-config" content="/assets/favicons/browserconfig.xml">
<meta name="theme-color" content="#f13a24">
```

> site.webmanifest

```
{
    "name": "Electronic Line",
    "short_name": "Electronic Line",
    "icons": [
        {
            "src": "/assets/favicons/android-chrome-192x192.png",
            "sizes": "192x192",
            "type": "image/png"
        },
        {
            "src": "/assets/favicons/android-chrome-256x256.png",
            "sizes": "256x256",
            "type": "image/png"
        }
    ],
    "theme_color": "#f13a24",
    "background_color": "#f13a24",
    "display": "standalone"
}

```

> safari-pinned-tab.svg

```
<?xml version="1.0" standalone="no"?>
<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 20010904//EN"
 "http://www.w3.org/TR/2001/REC-SVG-20010904/DTD/svg10.dtd">
<svg version="1.0" xmlns="http://www.w3.org/2000/svg"
 width="330.000000pt" height="330.000000pt" viewBox="0 0 330.000000 330.000000"
 preserveAspectRatio="xMidYMid meet">
<metadata>
Created by potrace 1.11, written by Peter Selinger 2001-2013
</metadata>
<g transform="translate(0.000000,330.000000) scale(0.100000,-0.100000)"
fill="#000000" stroke="none">
<path d="M0 1650 l0 -1650 1650 0 1650 0 0 1650 0 1650 -1650 0 -1650 0 0
-1650z m638 1188 l3 -58 -116 0 -115 0 0 -45 0 -45 91 0 90 0 -3 -47 -3 -48
-87 -3 -88 -3 0 -44 0 -45 120 0 120 0 0 -55 0 -55 -187 2 -188 3 -3 239 c-2
170 1 243 9 252 10 12 46 14 183 12 l171 -3 3 -57z m210 -135 l2 -193 100 0
100 0 0 -60 0 -60 -165 0 -165 0 0 249 c0 190 3 251 13 255 6 3 35 4 62 3 l50
-2 3 -192z m651 183 c7 -8 11 -34 9 -58 l-3 -43 -117 -3 -118 -3 0 -44 0 -45
91 0 90 0 -3 -47 -3 -48 -87 -3 -88 -3 0 -44 0 -45 120 0 120 0 0 -55 0 -55
-190 0 -190 0 0 243 c0 176 3 246 12 255 19 19 342 17 357 -2z m434 -5 c25
-11 47 -23 50 -28 7 -11 -51 -103 -65 -103 -5 0 -22 7 -39 15 -62 32 -149 -12
-169 -86 -14 -52 4 -112 44 -145 25 -22 37 -25 80 -21 28 2 61 8 72 12 18 6
27 -1 54 -40 l33 -48 -24 -18 c-64 -50 -187 -50 -266 -2 -106 65 -150 172
-118 289 42 155 204 237 348 175z m-391 -1017 c146 -49 219 -221 154 -359 -93
-197 -356 -203 -463 -10 -23 41 -28 62 -28 120 0 59 5 79 30 123 63 114 189
167 307 126z m1412 -11 c42 -20 46 -41 14 -86 -30 -44 -39 -48 -65 -32 -52 34
-145 7 -168 -49 -18 -43 -19 -113 -1 -144 17 -32 65 -62 99 -62 15 0 44 7 64
15 19 8 37 15 38 15 7 0 55 -82 55 -95 0 -21 -40 -44 -101 -57 -83 -17 -158 8
-225 75 -57 57 -74 99 -74 182 0 77 17 123 67 176 79 85 198 110 297 62z
m-2294 -43 l0 -60 -65 0 -65 0 -2 -192 -3 -193 -60 0 -60 0 -3 192 -2 192 -63
3 -62 3 -3 58 -3 57 196 0 195 0 0 -60z m400 40 c47 -24 90 -92 90 -141 -1
-44 -32 -112 -61 -133 l-24 -16 48 -89 c26 -48 47 -94 47 -100 0 -8 -20 -11
-67 -9 l-67 3 -46 80 c-37 65 -51 81 -73 83 l-26 3 -3 -83 -3 -83 -60 0 -60 0
-3 253 -2 252 135 0 c116 0 142 -3 175 -20z m903 -12 c13 -18 53 -80 88 -138
l64 -105 5 135 5 135 60 0 60 0 0 -250 0 -250 -62 -3 -61 -3 -83 138 c-45 76
-87 144 -93 152 -8 11 -12 -24 -16 -135 l-5 -149 -55 0 -55 0 -3 253 -2 252
64 0 c62 0 66 -2 89 -32z m525 -220 l-3 -253 -54 -3 c-37 -2 -57 1 -62 10 -10
15 -12 458 -3 482 5 12 21 16 65 16 l59 0 -2 -252z m-503 -728 c3 -6 -1 -13
-9 -16 -10 -4 -16 -18 -16 -36 0 -36 -23 -34 -28 3 -2 13 -8 29 -13 35 -5 6
-6 14 -2 17 10 11 61 8 68 -3z m52 -12 l10 -23 7 23 c11 35 30 21 37 -26 5
-42 -3 -57 -15 -25 -6 14 -8 14 -21 -3 -13 -16 -16 -17 -25 -4 -6 10 -10 11
-10 3 0 -7 -4 -13 -10 -13 -11 0 -14 73 -3 83 11 12 20 8 30 -15z m-1639 0 c8
-8 12 -66 12 -195 l0 -182 98 -3 97 -3 3 -52 3 -53 -170 0 -171 0 0 238 c0
172 3 241 12 250 7 7 33 12 58 12 25 0 51 -5 58 -12z m420 -240 l2 -248 -65 0
-65 0 0 243 c0 134 3 247 7 251 4 4 32 6 63 4 l55 -3 3 -247z m269 214 c16
-21 55 -84 88 -140 l60 -102 3 127 c1 75 7 132 14 140 7 9 28 13 62 11 l51 -3
3 -247 2 -248 -68 0 -68 0 -39 68 c-21 37 -59 100 -84 141 l-46 73 -3 -141 -3
-141 -64 0 -65 0 0 243 c0 134 3 247 7 250 3 4 33 7 65 7 56 0 59 -1 85 -38z
m760 21 c5 -14 7 -54 4 -93 -1 -6 -47 -10 -116 -10 l-115 0 0 -45 0 -44 93 -3
c88 -3 92 -4 95 -27 2 -12 1 -34 -2 -47 -6 -23 -10 -24 -96 -24 l-90 0 0 -45
0 -45 120 0 120 0 0 -50 0 -50 -185 0 -185 0 0 243 c0 134 3 247 7 250 3 4 82
7 175 7 148 0 169 -2 175 -17z"/>
<path d="M1399 1735 c-91 -49 -84 -206 11 -245 38 -16 105 -7 132 16 23 22 48
77 48 109 0 40 -30 95 -63 115 -37 23 -91 25 -128 5z"/>
<path d="M880 1700 l0 -60 49 0 c56 0 81 17 81 56 0 50 -16 64 -75 64 l-55 0
0 -60z"/>
</g>
</svg>
```

> It also generates "browserconfig.xml" which is not included in `<head>`

```
<?xml version="1.0" encoding="utf-8"?>
<browserconfig>
    <msapplication>
        <tile>
            <square70x70logo src="/assets/favicons/mstile-70x70.png"/>
            <square150x150logo src="/assets/favicons/mstile-150x150.png"/>
            <square310x310logo src="/assets/favicons/mstile-310x310.png"/>
            <wide310x150logo src="/assets/favicons/mstile-310x150.png"/>
            <TileColor>#da532c</TileColor>
        </tile>
    </msapplication>
</browserconfig>
```
