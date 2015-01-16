var esm = {};


esm.getSystem = function() {

    $.get('libs/system.php', function(data) {

        var $box = $('.box#esm-system .box-content tbody');

        esm.insertDatas($box, 'system', data);

    }, 'json');

}


esm.getLoad_average = function() {

    $.get('libs/load_average.php', function(data) {

        var $box = $('.box#esm-load_average .box-content');

        esm.reconfigureGauge($('input#load-average_1', $box), data[0]);
        esm.reconfigureGauge($('input#load-average_5', $box), data[1]);
        esm.reconfigureGauge($('input#load-average_15', $box), data[2]);

    }, 'json');

}


esm.getCpu = function() {

    $.get('libs/cpu.php', function(data) {

        var $box = $('.box#esm-cpu .box-content tbody');

        esm.insertDatas($box, 'cpu', data);

    }, 'json');

}


esm.getMemory = function() {

    $.get('libs/memory.php', function(data) {

        var $box = $('.box#esm-memory .box-content tbody');

        esm.insertDatas($box, 'memory', data);

        // Percent bar
        var $progress = $('.progressbar', $box);

        $progress
            .css('width', data.percent_used+'%')
            .html(data.percent_used+'%')
            .removeClass('green orange red');

        if (data.percent_used <= 50)
            $progress.addClass('green');
        else if (data.percent_used <= 75)
            $progress.addClass('orange');
        else
            $progress.addClass('red');

    }, 'json');

}


esm.getSwap = function() {

    $.get('libs/swap.php', function(data) {

        var $box = $('.box#esm-swap .box-content tbody');

        esm.insertDatas($box, 'swap', data);

        // Percent bar
        var $progress = $('.progressbar', $box);

        $progress
            .css('width', data.percent_used+'%')
            .html(data.percent_used+'%')
            .removeClass('green orange red');

        if (data.percent_used <= 50)
            $progress.addClass('green');
        else if (data.percent_used <= 75)
            $progress.addClass('orange');
        else
            $progress.addClass('red');

    }, 'json');

}


esm.getDisk = function() {

    $.get('libs/disk.php', function(data) {

        var $box = $('.box#esm-disk .box-content tbody');
        $box.empty();

        for (var line in data)
        {
            var bar_class = '';

            if (data[line].percent_used <= 50)
                bar_class = 'green';
            else if (data[line].percent_used <= 75)
                bar_class = 'orange';
            else
                bar_class = 'red';

            var html = '';
            html += '<tr>';
            html += '<td>'+data[line].mount+'</td>';
            html += '<td><div class="progressbar-wrap"><div class="progressbar '+bar_class+'" style="width: '+data[line].percent_used+'%;">'+data[line].percent_used+'%</div></div></td>';
            html += '<td class="t-right">'+data[line].free+'</td>';
            html += '<td class="t-right">'+data[line].used+'</td>';
            html += '<td class="t-right">'+data[line].total+'</td>';
            html += '</tr>';

            $box.append(html);
        }

    }, 'json');

}


esm.getLast_login = function() {

    $.get('libs/last_login.php', function(data) {

        var $box = $('.box#esm-last_login .box-content tbody');
        $box.empty();

        for (var line in data)
        {
            var html = '';
            html += '<tr>';
            html += '<td>'+data[line].user+'</td>';
            html += '<td class="w50p">'+data[line].date+'</td>';
            html += '</tr>';

            $box.append(html);
        }

    }, 'json');

}


esm.getNetwork = function() {

    $.get('libs/network.php', function(data) {

        var $box = $('.box#esm-network .box-content tbody');
        $box.empty();

        for (var line in data)
        {
            var html = '';
            html += '<tr>';
            html += '<td>'+data[line].interface+'</td>';
            html += '<td>'+data[line].ip+'</td>';
            html += '<td class="t-right">'+data[line].receive+'</td>';
            html += '<td class="t-right">'+data[line].transmit+'</td>';
            html += '</tr>';

            $box.append(html);
        }

    }, 'json');

}


esm.getPing = function() {

    $.get('libs/ping.php', function(data) {

        var $box = $('.box#esm-ping .box-content tbody');
        $box.empty();

        for (var line in data)
        {
            var html = '';
            html += '<tr>';
            html += '<td>'+data[line].host+'</td>';
            html += '<td>'+data[line].ping+' ms</td>';
            html += '</tr>';

            $box.append(html);
        }

    }, 'json');

}


esm.getServices = function() {

    $.get('libs/services.php', function(data) {

        var $box = $('.box#esm-services .box-content tbody');
        $box.empty();

        for (var line in data)
        {
            var label_color  = data[line].status == 1 ? 'success' : 'error';
            var label_status = data[line].status == 1 ? 'online' : 'offline';

            var html = '';
            html += '<tr>';
            html += '<td class="w15p"><span class="label '+label_color+'">'+label_status+'</span></td>';
            html += '<td>'+data[line].name+'</td>';
            html += '<td class="w15p">'+data[line].port+'</td>';
            html += '</tr>';

            $box.append(html);
        }

    }, 'json');

}




esm.getAll = function() {
    esm.getSystem();
    esm.getCpu();
    esm.getLoad_average();
    esm.getMemory();
    esm.getSwap();
    esm.getDisk();
    esm.getLast_login();
    esm.getNetwork();
    esm.getPing();
    esm.getServices();
}

esm.reloadBlock = function(block) {
    esm.mapping[block]();
}

esm.insertDatas = function($box, block, datas) {
    for (var item in datas)
    {
        $('#'+block+'-'+item, $box).html(datas[item]);
    }
}

esm.reconfigureGauge = function($gauge, newValue) {
    // Change colors according to the percentages
    var colors = { green : '#7BCE6C', orange : '#E3BB80', red : '#CF6B6B' };
    var color  = '';

    if (newValue <= 50)
        color = colors.green;
    else if (newValue <= 75)
        color = colors.orange;
    else
        color = colors.red;

    $gauge.trigger('configure', { 
        'fgColor': color,
        'inputColor': color,
        'fontWeight': 'normal',
        'format' : function (value) {
            return value + '%';
        }
    });

    // Change gauge value
    $gauge.val(newValue).trigger('change');
}


esm.mapping = {
    all: esm.getAll,
    system: esm.getSystem,
    load_average: esm.getLoad_average,
    cpu: esm.getCpu,
    memory: esm.getMemory,
    swap: esm.getSwap,
    disk: esm.getDisk,
    last_login: esm.getLast_login,
    network: esm.getNetwork,
    ping: esm.getPing,
    services: esm.getServices
};