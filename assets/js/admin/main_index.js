"use strict";
(function(window) {
    var photo = window.photo; 
    photo._init_chart = function(data){
        
    Highcharts.chart('container', {
        chart: {
            type: 'column'
        },
        title: {
            text: '7日统计数据'
        },
        xAxis: {
            categories: data.cacu_date,
            crosshair: true
        },
        yAxis: {
            min: 0,
            title: {
                text: ''
            },
        },
        tooltip: {
            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                '<td style="padding:0"><b>{point.y:.0f}</b></td></tr>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0
            }
        },
        series: [{
            name: '关注数',
            data: data.user_data,
            color:"#26B99A"
        }, {
            name: '会员数',
            data: data.member_data,
            color: "#5bc0de"
        }, {
            name: '新增作品数',
            data: data.publish_data,
            color: "#f0ad4e"
        }, {
            name: '推广人数',
            data: data.agent_data,
            color: "#337ab7"
        }, {
            name: '推荐成功的会员',
            data: data.agent_member_data,
            color: "#d9534f"
        }]
    });
    }  
 
      photo.RequestDataPost({
        request_url : '/admin/main/get_data',
        data        : {},
        callback_data : function(data){
            photo._init_chart(data);
        }
      });
})(window)