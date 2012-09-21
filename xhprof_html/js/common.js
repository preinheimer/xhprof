function bytesToSize(bytes, precision)
{
    var kilobyte = 1024;
    var megabyte = kilobyte * 1024;
    var gigabyte = megabyte * 1024;
    var terabyte = gigabyte * 1024;
 
    if ((bytes >= 0) && (bytes < kilobyte)) {
        return bytes + ' B';
 
    } else if ((bytes >= kilobyte) && (bytes < megabyte)) {
        return (bytes / kilobyte).toFixed(precision) + ' KB';
 
    } else if ((bytes >= megabyte) && (bytes < gigabyte)) {
        return (bytes / megabyte).toFixed(precision) + ' MB';
 
    } else if ((bytes >= gigabyte) && (bytes < terabyte)) {
        return (bytes / gigabyte).toFixed(precision) + ' GB';
 
    } else if (bytes >= terabyte) {
        return (bytes / terabyte).toFixed(precision) + ' TB';
 
    } else {
        return bytes + ' B';
    }
}
 
function usecToSize(usec, precision)
{
    var millisec = 1024;
    var second = millisec * 1000;
    var minute = second * 60;
    var hour = minute * 60;
 
    if ((usec >= 0) && (usec < millisec)) {
        return usec + ' µs';
 
    } else if ((usec >= millisec) && (usec < second)) {
        return (usec / millisec).toFixed(precision) + ' ms';
 
    } else if ((usec >= second) && (usec < minute)) {
        return (usec / second).toFixed(precision) + ' s';
 
    } else if ((usec >= minute) && (usec < hour)) {
        return (usec / minute).toFixed(precision) + ' m';
 
    } else if (usec >= hour) {
        return (usec / hour).toFixed(precision) + ' h';
 
    } else {
        return usec + ' µs';
    }
}