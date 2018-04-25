<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>FGX Chat</title>

        <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" media="screen" title="no title" charset="utf-8"/>
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-md-push-2 col-md-8">
                    <h2>
                        core Chat
                    </h2>
                    <h3>
                        Messages
                    </h3>
                    <ul class="message-list">
                    
                    </ul>
                    <form action="index.html" class="chatForm" method="post">
                        <div class="form-group">
                            <label for="message">Message</label>
                            <textarea type="button" id="message" class="form-control" value=""></textarea>
                        </div>
                        <div class="">
                            <button type="submit" name="button" class="btn btn-primary pull-right">Send</button>
                        </div>
                    </form>
                </div>
            </div>
        </div> 
    </body>
    <script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
    <script src="js/main.js"></script>
</html>
