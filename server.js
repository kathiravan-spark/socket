const express = require('express');

const app = express();

const mysql = require("mysql");


const server = require('http').createServer(app);


const io = require('socket.io')(server, {
    cors: { origin: "*"}
});
var mysqlTimestamp = "2021-22-3";
/** storing db configuration */
var connection = mysql.createConnection({
    host     : 'localhost',
    user     : 'root',
    password : '12345',
    database : 'socket-chat'
  });
   
connection.connect();
app.get('/',function(req,res){
	res.end("Welcome to Node.js Socket");
})
// var mysqlTimestamp = moment().utc().format('YYYY-MM-DD HH:mm:ss');

io.on('connection', (socket) => {
    console.log('connection');

    socket.on('sendChatToServer', (message) => {
        var query = `INSERT INTO chats (from_id,to_id,message) VALUES ('${message.from}', '${message.to}','${message.msg}')`
        connection.query(query, (err, result) => {  
            if (err) {  
                console.log('message not inserted', err.message)
                return
            }else{
                console.log('message inserted to db', result.insertId)
            } 
        }); 
        // io.sockets.emit('sendChatToClient', message);
        socket.broadcast.emit('sendChatToClient', message);
    });


    socket.on('disconnect', (socket) => {
        console.log('Disconnect');
    });
});

server.listen(3000, () => {
    console.log('Server is running');
});