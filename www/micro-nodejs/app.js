var express = require('express');
var app = express();

app.use(remoteAuthMiddleware);
app.get('/path', function (req, res) {
    console.log('user is ', req.user);
    res.send('Hello Path!');
});

app.get('*', function (req, res) {
    console.log('user is ', req.user);
    res.send('Hello World!');
});

app.listen(3000, function () {
    console.log('Example app listening on port 3000!');
});

/*
 * 
 * Custom functions
 * 
 */

function remoteAuthMiddleware(req, res, next){
    remoteAuthRequest("oiubi", function(err, user){
        if(err)
        {
            console.log('remoteAuthMiddleWare error', err);
        }
        req.user = user;
        next();
    });
}

function remoteAuthRequest(token, callback){
    console.log('auth token', token);
    callback(undefined, {
        "id"    : 1372,
        "name"  : "Kevin"
    });
}
