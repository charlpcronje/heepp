'use strict';

core.notify = class {
    constructor(type,message) {
        // success, info, error
        this.notifyType        = type;
        this.notifyHeading     = 'Successful';
        this.notifyImage       = `<img style="width:50px" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGQAAABkCAMAAABHPGVmAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAqlQTFRFAHMucLCKi7+gAnQwJohNdLON2Orff7mWfbiUjsGi+vz7BHUxeraSjcCiLYxTncmvB3c0stXAmMarI4ZLU6FyV6N1Yah+0+fbYql+h72diL6d3OziBnYzb7CJhbybv9zLCXg1crKLe7eTeLWQHYNGfLeUfriVFH4+n8qwIYVJ7fXwGIBCudnGUZ9wutnGd7SQ3ezjdrSPIoZKnMmu/f79VqJ0HIJFUqBxZaqBaKyDksOm8ff0a66GA3Uw8PfzJYdMAXQvu9rHC3k3DHo49fr3WaR32erggLmX5/Lr3+3l+Pv5y+LUl8aqOZJdlMSnt9fEX6d8EHw77/byQJZiCng2nsqvQ5hl+fz6Qpdk0ubaSZtqBXYymsisc7KMwNzLN5FbVaJ0/P391OfcJIdM8vj0p8+3tNbC6vPuRZln6fPtg7uZ2uvhba+H6/TvVKFzLoxU4u/n0ebZ7vbxMY5W9/v4SJtpib6eq9G6WqR4Z6yC1ejdXKZ5FX8/+/38EXw87PXvgrqZOpNeo8y0wt7NgbqY4e/mocuybK6HlsWpP5ZiNpFam8itdbOODns5PZRgGoFDO5Ne5vHrx+DRpc61ZquCF4BBCHc16PLss9XBKIlPS5xrWKN2lcWoUJ9wRJhmGYFCebWR4/Dow97Oaa2EDXo5T55vL41VttfD2+vhrtO9MI1VSpxr1ujdxN/Pk8SmzeTWjMChKopQH4RHHoNHOJJcRplnhLuaIIVI5fHqG4JEzOPVmcerE30+pM20K4tRZKqAPpVhM49Y4O7moMuxkMKkQZdjr9O9zuTX9vr4Tp5uTZ1tvdvJkcOlPJRfir+f5PDpFn9AD3s6xuDQNZBZMo5XrNG7Y6l/ps62En099Pn2J4hOsNS+8/j1yuLUuNjFaq2FLItSosyz////HJAQlgAAAON0Uk5T/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////wDMXgGwAAAGW0lEQVR42qya50MUORTAQ1uQhaWDVKmiCEhVAQVBQEFERKVYQBEVUATEevbePfWsZz/b2c6zXO+9997v8pfc7rqTMpPJJLO+T5PkJb9NNuXlvQAoKkuzd3teimh5ZLO178tKig660S9cFYgoFbQNyfIOBirxeqUr5nTlk4H0jggfA3Ql/aW9NW5DfiqcBAxk4u2gSjcgBaGLgJCc2NloElJbnAeEZXNatxlI6BggJZN6NslCduwH0pK3VgrSPB6YkvBvxCGt3uw2Zt3vWtEQGRlZuHpQZ84d+lsUMo9Re0aWz8dRGVindFl22oKnGYp+YpAiTcWRV8pLWb/Q/1jKdY3ys6XGkM5p6rW2IZu3Bv7655CqQmq1EeTPMNUGtd5w19jiN4OuE3yHD2lULfGhsSIb4MoUL3rJdPAgJXQ/vjomupkvC6cqJkbpQ1ZRDOsQKCFrR1EjZtGF/EDqtT8PpaSR6syvnTqQcaTWWCgtPmT9aWzICFInF5qQULIFHxYkh9Q4B01JJjnLQrSQheTOvhialKkTiFb6NRDyD6mDpqWN9bcokAH2aMoL+c/+ooKcwUXXoFuShls6WUtBCP7b0E1ZgNtaTkIKruKCKe5CILH2LxOQOpxd7DYD7sCtRRKQdJRrk2/TEqXOicCUDAQ5gDPvSDNSALiiyvp0Lj4rEAR3ZJ00Y76jWrwqM5DuigMyXdU7GRnPNh/wWZnggmShnJ9lGZeUmvl0fj5q0vsxpBPvNyslGQF4DP6jSzaiggEnZDtKrzDPAGA2VZSA8g86IedR+jk5xmTa6KAs4aUou8wJ+U5JjpJj+KrNuqlk6bsou8YOuaveaQQlQWM7/s4+JYPskBdRaph7DGAly6eg7AY75DMlUVErwYhh2NqhlMZ7eBIDOFJJfC7BGM5geNIq8Up+XCPIiFMSo5nNHWVt/Z4MRoBKZzEqyQTZDOuC/Mlz59YLMaI1mzMqmgdmo+9qBsPDUaA2V4cwGIGaqr8lKmU9AE2u4BIdBgCTqcwXhBgQblMKq0Cu8nmfs9y+JjKLGQxf1kijo+sIOKt8ruZNITxi5xiMZOaUQZbcIJipfBbqjBVN2Ws8d6njzLlQQDtemByGQpktzoB+6F4LbNRprHMPUA6/fAkG3qRTwb9sSLW2taS2BIE1yITsp00kdAkUdHRECxwFI8Fh5TNCY+kISCBnfzuIji0wFnkS+H+8NAN7Nq4D1NYtHWuHIzHcnboK3VPAGnScaByJewwYyfzj4CN0xwXYRM2BUGrEPPmM5s2KYhpYgiqVQymKAYO46IaCV714u1y8aQYMwQcVgGN0Ny+HDNVhBIhY+y5H01EAm5TExoUQio5YtLEZsE/RfWA3JLayjTMuJdCYUUoYvwBmGk1JDzMM+AzSrrdDmtFZvIh/BxFcgy65htSjHLZwE5Fkyx6ZNfhYKpGlddFpcGPDoEiviofM3HXKG0h/vROCrdZgf2OKGAOGkfcRx3Wuhes5plelIOMGIO8jgJoIE3S74lqVAVC2I0kuiH+ciMPOMWLDBRnYDga7lHv8Tpy3VL/mOLBG1OjHjoE5yFmwEEfeujhVhS9Jo/GPjsW+lWScu8R9B04soDvigpRMxDEMf7chNgxpJZ1qL+P82+4yiME6T7sH33xCLkgIdxO7QzcNIZw4YJ47jGGMnRS5bFcThV+YZ9QQzTzU+IW3xBGRmdfNMk5dZHkoALOf1nJzDAsZSvuSFRCg7uZBZhjlJ9lhCzK00UJS0uQZH5D1X9OJn6w6QWptqDW/Puxi0YsExSZSodzpMoh7RyhGtn7g7KlZlGbDKVGEP90N1Q6oCgF2VFC6V9dsEgs1PKAZIbwQoL0vVlr9YV2BIeLmj4DL0IZlO0apA79buQHN7uJbattP819qA8y9TRqLcU69Tjjw8vazs9TKZa3QGMK4wtvlUfxbA5TXeJXlj/kXvLSKVYzziBn0v2llmvIV28LfSfLz9fX1OR4xM519pfiQ1R77+YLlgrnnC31sM0DvIUbQt/IIq96tRfdJSUmSLKMqB8pC7GNWNEECseCufkvcZz7vH7cKIrI+4bVj8GCpP7rPmOCdG8Vvxfjp1fcBNh6hbHmmYRNCj8iqgyJtiYw3V94R9fdE6gPRzbw3M79n3czBM2GpqXnefYfD/epO7xKt+78AAwCJ+OE65etoZQAAAABJRU5ErkJggg==" alt="Success Icon"/>`;
        this.notifyPosition    = 'top: 10px; right: 10px';
        this.notifyMessage     = message;
        this.notifyWidth       = '250';
        this.notifyBGColor     = '#f7f7f7 !important';
        this.notifyColor       = '#828282 !important';
        this.notifyBoxShadow   = 'rgba(0, 0, 0, 0.380392) 0px 8px 14px 0px';
        this.border            = '1px solid #88a910';
        this.notifyRadius      = '3px';
        this.notifyTimeOut     = '4000';
        this.notifyElem        = "";
    }

    create() {
        if (this.notifyType === 'success') {
            this.notifyHeading      = 'SUCCESS';
            this.notifyBGColor      = '#badc52';
            this.notifyColor        = '#828282 !important';
            this.notifyBoxShadow    = 'rgba(0, 0, 0, 0.380392) 0px 8px 14px 0px';
            this.border             = '1px solid #88a910';
            this.notifyImage        = '<img style="width:50px" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGQAAABkCAMAAABHPGVmAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAqlQTFRFAHMucLCKi7+gAnQwJohNdLON2Orff7mWfbiUjsGi+vz7BHUxeraSjcCiLYxTncmvB3c0stXAmMarI4ZLU6FyV6N1Yah+0+fbYql+h72diL6d3OziBnYzb7CJhbybv9zLCXg1crKLe7eTeLWQHYNGfLeUfriVFH4+n8qwIYVJ7fXwGIBCudnGUZ9wutnGd7SQ3ezjdrSPIoZKnMmu/f79VqJ0HIJFUqBxZaqBaKyDksOm8ff0a66GA3Uw8PfzJYdMAXQvu9rHC3k3DHo49fr3WaR32erggLmX5/Lr3+3l+Pv5y+LUl8aqOZJdlMSnt9fEX6d8EHw77/byQJZiCng2nsqvQ5hl+fz6Qpdk0ubaSZtqBXYymsisc7KMwNzLN5FbVaJ0/P391OfcJIdM8vj0p8+3tNbC6vPuRZln6fPtg7uZ2uvhba+H6/TvVKFzLoxU4u/n0ebZ7vbxMY5W9/v4SJtpib6eq9G6WqR4Z6yC1ejdXKZ5FX8/+/38EXw87PXvgrqZOpNeo8y0wt7NgbqY4e/mocuybK6HlsWpP5ZiNpFam8itdbOODns5PZRgGoFDO5Ne5vHrx+DRpc61ZquCF4BBCHc16PLss9XBKIlPS5xrWKN2lcWoUJ9wRJhmGYFCebWR4/Dow97Oaa2EDXo5T55vL41VttfD2+vhrtO9MI1VSpxr1ujdxN/Pk8SmzeTWjMChKopQH4RHHoNHOJJcRplnhLuaIIVI5fHqG4JEzOPVmcerE30+pM20K4tRZKqAPpVhM49Y4O7moMuxkMKkQZdjr9O9zuTX9vr4Tp5uTZ1tvdvJkcOlPJRfir+f5PDpFn9AD3s6xuDQNZBZMo5XrNG7Y6l/ps62En099Pn2J4hOsNS+8/j1yuLUuNjFaq2FLItSosyz////HJAQlgAAAON0Uk5T/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////wDMXgGwAAAGW0lEQVR42qya50MUORTAQ1uQhaWDVKmiCEhVAQVBQEFERKVYQBEVUATEevbePfWsZz/b2c6zXO+9997v8pfc7rqTMpPJJLO+T5PkJb9NNuXlvQAoKkuzd3teimh5ZLO178tKig660S9cFYgoFbQNyfIOBirxeqUr5nTlk4H0jggfA3Ql/aW9NW5DfiqcBAxk4u2gSjcgBaGLgJCc2NloElJbnAeEZXNatxlI6BggJZN6NslCduwH0pK3VgrSPB6YkvBvxCGt3uw2Zt3vWtEQGRlZuHpQZ84d+lsUMo9Re0aWz8dRGVindFl22oKnGYp+YpAiTcWRV8pLWb/Q/1jKdY3ys6XGkM5p6rW2IZu3Bv7655CqQmq1EeTPMNUGtd5w19jiN4OuE3yHD2lULfGhsSIb4MoUL3rJdPAgJXQ/vjomupkvC6cqJkbpQ1ZRDOsQKCFrR1EjZtGF/EDqtT8PpaSR6syvnTqQcaTWWCgtPmT9aWzICFInF5qQULIFHxYkh9Q4B01JJjnLQrSQheTOvhialKkTiFb6NRDyD6mDpqWN9bcokAH2aMoL+c/+ooKcwUXXoFuShls6WUtBCP7b0E1ZgNtaTkIKruKCKe5CILH2LxOQOpxd7DYD7sCtRRKQdJRrk2/TEqXOicCUDAQ5gDPvSDNSALiiyvp0Lj4rEAR3ZJ00Y76jWrwqM5DuigMyXdU7GRnPNh/wWZnggmShnJ9lGZeUmvl0fj5q0vsxpBPvNyslGQF4DP6jSzaiggEnZDtKrzDPAGA2VZSA8g86IedR+jk5xmTa6KAs4aUou8wJ+U5JjpJj+KrNuqlk6bsou8YOuaveaQQlQWM7/s4+JYPskBdRaph7DGAly6eg7AY75DMlUVErwYhh2NqhlMZ7eBIDOFJJfC7BGM5geNIq8Up+XCPIiFMSo5nNHWVt/Z4MRoBKZzEqyQTZDOuC/Mlz59YLMaI1mzMqmgdmo+9qBsPDUaA2V4cwGIGaqr8lKmU9AE2u4BIdBgCTqcwXhBgQblMKq0Cu8nmfs9y+JjKLGQxf1kijo+sIOKt8ruZNITxi5xiMZOaUQZbcIJipfBbqjBVN2Ws8d6njzLlQQDtemByGQpktzoB+6F4LbNRprHMPUA6/fAkG3qRTwb9sSLW2taS2BIE1yITsp00kdAkUdHRECxwFI8Fh5TNCY+kISCBnfzuIji0wFnkS+H+8NAN7Nq4D1NYtHWuHIzHcnboK3VPAGnScaByJewwYyfzj4CN0xwXYRM2BUGrEPPmM5s2KYhpYgiqVQymKAYO46IaCV714u1y8aQYMwQcVgGN0Ny+HDNVhBIhY+y5H01EAm5TExoUQio5YtLEZsE/RfWA3JLayjTMuJdCYUUoYvwBmGk1JDzMM+AzSrrdDmtFZvIh/BxFcgy65htSjHLZwE5Fkyx6ZNfhYKpGlddFpcGPDoEiviofM3HXKG0h/vROCrdZgf2OKGAOGkfcRx3Wuhes5plelIOMGIO8jgJoIE3S74lqVAVC2I0kuiH+ciMPOMWLDBRnYDga7lHv8Tpy3VL/mOLBG1OjHjoE5yFmwEEfeujhVhS9Jo/GPjsW+lWScu8R9B04soDvigpRMxDEMf7chNgxpJZ1qL+P82+4yiME6T7sH33xCLkgIdxO7QzcNIZw4YJ47jGGMnRS5bFcThV+YZ9QQzTzU+IW3xBGRmdfNMk5dZHkoALOf1nJzDAsZSvuSFRCg7uZBZhjlJ9lhCzK00UJS0uQZH5D1X9OJn6w6QWptqDW/Puxi0YsExSZSodzpMoh7RyhGtn7g7KlZlGbDKVGEP90N1Q6oCgF2VFC6V9dsEgs1PKAZIbwQoL0vVlr9YV2BIeLmj4DL0IZlO0apA79buQHN7uJbattP819qA8y9TRqLcU69Tjjw8vazs9TKZa3QGMK4wtvlUfxbA5TXeJXlj/kXvLSKVYzziBn0v2llmvIV28LfSfLz9fX1OR4xM519pfiQ1R77+YLlgrnnC31sM0DvIUbQt/IIq96tRfdJSUmSLKMqB8pC7GNWNEECseCufkvcZz7vH7cKIrI+4bVj8GCpP7rPmOCdG8Vvxfjp1fcBNh6hbHmmYRNCj8iqgyJtiYw3V94R9fdE6gPRzbw3M79n3czBM2GpqXnefYfD/epO7xKt+78AAwCJ+OE65etoZQAAAABJRU5ErkJggg==" alt="Success Icon"/>';
        } else if (this.notifyType === 'info') {
            this.notifyHeading      = 'INFORMATION';
            this.notifyBGColor      = '#f4db05';
            this.notifyColor        = '#828282 !important';
            this.notifyBoxShadow    = 'rgba(0, 0, 0, 0.380392) 0px 8px 14px 0px';
            this.border             = '1px solid #b39c00';
            this.notifyImage        = '<img style="width:50px" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGQAAABkCAMAAABHPGVmAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAAZQTFRFtaEC////CJBjtgAAAAJ0Uk5T/wDltzBKAAABHElEQVR42uzaQQ7DMAhE0c/9L1112cRJDIZJlOI16O1sCwabPAzOdG8U8EAsCZMOq8KMQwZxxZBDnDNkEWcMicahQiJxyJBrjBWSjaFCMjFkyDf2CgXGTqHC2CqUGBsFp+Es3COeTpfiQixYTNBwlRN+AR31xF/Z+Q4pQgWCBOEHQYBQheBFWENiV+t8E9F/Ik4kcoG7eoj9RilFQs8X1cZXeRNC+WnEiTztWNFvs5FGGmmkkUb+DbF6xBpxIlaN2LsQq0XsbcijJhJriNUhJkasCjE5YjWIf2RLdJgqRiRjdM1C4LrNh9iNiGUi9y7ONCtAzTJTs5bVLJg1q3LR0l8TX9AEMUSREk04RhTzEQWWRNErVYgsIQ73EWAAzeUK/pjiNswAAAAASUVORK5CYII=" alt="Info Icon"/>';
        } else if (this.notifyType === 'error') {
            this.notifyHeading      = 'ERROR';
            this.notifyBGColor      = '#f05a5c';
            this.notifyColor        = '#828282 !important';
            this.notifyBoxShadow    = 'rgba(0, 0, 0, 0.380392) 0px 8px 14px 0px';
            this.border             = '1px solid red';
            this.notifyImage        = '<img style="width:50px" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGQAAABjCAMAAABaOVXeAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAFdQTFRF0A8K65iW7J2a5YKA7qWk5Xp354WC6ZOR7qim7J+d5Hp354B965qY5XVz4nNx6piW6YqI6YyK6paT7KGf6IWD6o6M6IyK43Nw43d06pOR5nd06IKA////1Iv0HAAAAB10Uk5T/////////////////////////////////////wBZhudqAAACgUlEQVR42sya23LCMAxENxcSoNwKBQr4/7+zlKEMBVuRVqLTfWztOVFQJFkyklJVhwed1tq9UAHmKGkbA+kwoJEX0kKlygGpodYHCTEgvrViIDBrY4WAkgkyAal3PeQIXloIXNJB4FQ1DKng1tsQZI0AzWXIFjGSIBXwCgpMDL13nIoQo2uqfQzKTfboM85CCIa4Kwd5szilCvMMGdPZIg1GS2jdpU12zCPkyOYKDQX6qChSFpIfwxB5k92Ye8gGfsoyt6O7g/gLkrN2xeeCqfipRUqT2XG8QdzliPCz/EBavJDyfoVElFZlShmS4j7K9gJZZcM0V1rnTUHJxJ6LYjs15LL8M+qFtWdIXQwGFKXJlGFIQoSmKDkvSlJGYyg6SKPIrB8WSo1KesolE8WeQzE68VUwLkZUmHZKr2Ask5dCREA7hQmzxZVdIGRmdTEqYSyML4w8vdoo7EHctHpB5lfT8jmbxC0tNbpSsLgYX45MAz4sz3ccCNFTFJCJm+KrEXUulunITX35+6q1vKi3QBqN+Rk7nv92YEyRmg5LsSSiKNa6i6JkIX0UZVRoZy2EgttOqfP/neQhe/P58/ZwdeHoMA0zBeVDUEIcJdNgnBUhG5ZSPJjOzKYYKFOpWRBFuXUkdvZxWGOGZJ/rEGLKXZeoHwrfNGWwqbb1U3517sbMmHKY0ah6kE6KsmXbelzsqS/cxzQGZEjST4/McwrVQGBEUeyjjWzJN1aWb/ohzeM09CCv/izOtDTf135vbjmATxTk4CyOMjAxfQEjIcVT/mLA/H9G5T6M4Y5EJEK47RGHEO+thDESogpF8HeJgqYqKUXVJM77XR6AGkLWyzd9CTAAHl7IPC2SjfkAAAAASUVORK5CYII=" alt="Error Icon"/>';
        }
        this.notifyElem = `<div class="coreNotification"
                                style="
                                       background-color: ${this.notifyBGColor};
                                       position: fixed;
                                       box-sizing: border-box;
                                       ${this.notifyPosition};
                                       overflow: hidden;
                                       border-radius: ${this.notifyRadius};
                                       border: ${this.border};
                                       box-shadow: ${this.notifyBoxShadow};
                                       display: none;
                                       z-index: 999999">
                                <div style="width: 60px;
                                            height: 100%;
                                            float: left;
                                            padding: 5px">
                                    ${this.notifyImage}
                                </div>
                                <div style="width: 200px;
                                            float: left;
                                            padding-left: 5px;
                                            padding-bottom: 5px;
                                            background-color: #f7f7f7;">
                                    <div style="color: ${this.notifyColor};
                                                font-weight: bold;">
                                        ${this.notifyHeading}
                                    </div>
                                    <div style="color: ${this.notifyColor};
                                                line-height: 15px;
                                                min-height: 35px">
                                        ${this.notifyMessage}
                                    </div>
                                </div>
                            </div>`;
    }

    show() {
        $('body').append(this.notifyElem);
        $(".coreNotification").fadeIn('slow');
    }

    hide() {
        var self = this;
        setTimeout(function() {
            $(".coreNotification").fadeOut('slow',function() {
                self.destroy();
            });
        },this.notifyTimeOut);
    }

    destroy() {
        $(".coreNotification").remove();
    }

    init(type,message) {
        this.notifyType     = type;
        this.notifyMessage  = message;
        this.create();
        this.show();
        this.hide();
    }
};
