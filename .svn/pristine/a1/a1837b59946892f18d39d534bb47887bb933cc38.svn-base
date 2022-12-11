/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var saveState = (function (datas, lsName) {
    var cron = JSON.parse(localStorage.getItem(lsName))
    if (!navigator.onLine) {
        if (cron === null) {
            cron = [datas]
        } else {
            if ($.inArray(datas, cron) === -1) {
                cron.push(datas)
            }
        }
        localStorage.setItem(lsName, JSON.stringify(cron));
        notify(-1, "La connexion a été perdue. Les changement seront appliquées lorsque vous serez à nouveau connecté à internet")
        throw new Error("La connexion a été perdue !");
    }
});

var cronRun = (function (xhrUrl, lsName) {
    var cron = JSON.parse(localStorage.getItem(lsName)), removed = [];
    $.each(cron, function (i, v) {
        $.get(xhrUrl, v).done();
        removed.push(i);
    });

    localStorage.setItem(lsName, JSON.stringify(_splice(removed, cron)));
});


var _splice = (function (spliced, removed) {
    $.each(removed, function (i, v) {
        spliced.splice(v, 1);
    });

    return spliced;
});