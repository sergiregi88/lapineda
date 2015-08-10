<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <h1>Hello </h1>
        <h2>Verify Your Email Address</h2>

        <div>
            Thanks for creating an account with the verification demo app.
            Please follow the link below to verify your email address
            {{ URL::to('http://lapinedafront.app/#/register/verify/'.$id.'/'.$activation_code) }}.<br/>
        // redirigir amb angular a get lapinedaback.app/api/auth/register/verify/$id/$activation_code
        </div>

    </body>
</html>