
//Получение всеx get параметров в виде объекта: $_GET()
//Получение значения get параметра: $_GET('param')
//Получение значения get параметра массива, нужно указать вложенность в виде массива: $_GET(['param', 'key'])
function $_GET(keys) {
    function getElement(arr, keys) {
        let key = keys.shift();
        return keys.length ? getElement(arr[key], keys) : arr[key];
    }

    function setElement(arr, keys, value) {
        let key = keys.shift();
        if (keys.length) {
            arr[key] = {};
            setElement(arr[key], keys, value)
        } else {    
            if (!key) {
                key = 0;
                while (key in arr) {
                    key++;
                }
            }
            arr[key] = value;
        }
    }

    let get = {};
    window.location.search.slice(1).split('&').forEach(function(item) {
        let data = item.split('=');
        let key = data[0].replace(/\[.*/, '');
        let value = data[1] ? data[1] : '';
        if (data[0] !== key) {
            let subkeys = data[0].match(/(?<=\[).*?(?=\])/g);
            get[key] = get[key] ? get[key] : {};
            setElement(get[key], subkeys, value);
        } else {
            get[key] = value;
        }
    });

    if (keys) {
        return getElement(get, keys.constructor !== Array ? keys.split() : keys);
    }

    return get;
}

//id = setInterval(function () {
//  counter--;
//  if (counter < 0) {
//    newElement.parentNode.replaceChild(downloadButton, newElement);
//    clearInterval(id);
//  } else {
//    newElement.innerHTML = "Загрузка начнётся... " + counter.toString();
//  }
//}, 1000);


/*
var x = new XMLHttpRequest();
x.open("GET", "php/testBase.php", true);
x.onload = function (){
    //console.log("===>" + x.responseText);
    //var reqData = JSON.parse(x.responseText);
    //var newHTML = '';
    //console.log(reqData.total);
    //console.log(reqData.total_pages);
    //console.log(reqData.results);
    //reqData.results.forEach(function(item, i) {
    //console.log(item.urls.small);
    //  newHTML += '<img src="' + item.urls.small + '"><br><br>';
    //});
    //document.write(newHTML);
    $(".bodyOfListApplication__name").html(x.responseText);
}
x.send(null);
*/