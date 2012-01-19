/*
 * mxCalendar AJAX navigation
 * by http://charlesmx.com
 * for ModX Revo
 */
$(function() { 
    var mxcCalPreContent = '';
    var mxcCalNexContent = '';
    var todayContent = '';
    var mxcCalPrev;
    var mxcCalNext;
    var urlParams = {};
    var mxcHistory = {};
    (function () {
        var e,
            a = /\+/g,  // Regex for replacing addition symbol with a space
            r = /([^&=]+)=?([^&]*)/g,
            d = function (s) { return decodeURIComponent(s.replace(a, " ")); },
            q = window.location.search.substring(1);

        while (e = r.exec(q))
           urlParams[d(e[1])] = d(e[2]);
    })();
    
    function ajaxmxc(){
        mxcCalPrev = document.getElementById("mxcprevlnk");
        mxcCalNext = document.getElementById("mxcnextlnk");
        if(mxcCalPrev) 
            $.get(mxcCalPrev.href+"&imajax=1", {},
               function(data){
                 mxcCalPreContent = data;
               });
        if(mxcCalNext) 
            $.get(mxcCalNext.href+"&imajax=1", {},
               function(data){
                 mxcCalNexContent = data;
               });
        
        if(modalActive){
            Shadowbox.teardown('.mxcmodal');
            Shadowbox.clearCache();
            Shadowbox.setup(".mxcmodal", {
                            modal: true
            });
        }
    }
    function addHistory(url){
        var stateObj = {};
        if(url)
        history.pushState(stateObj, "Calendar", url);
    }
    $("#mxcnextlnk").live("click",function(){
        $("#calbody").parent().html(mxcCalNexContent);
        //addHistory(mxcCalNext);
        ajaxmxc();
        return false;
    });
    $("#mxcprevlnk").live("click",function(){
        $("#calbody").parent().html(mxcCalPreContent);
        //addHistory(mxcCalPrev);
        ajaxmxc();
        return false;
    });
    $("#mxctodaylnk").live("click",function(){
        $("#calbody").parent().html(todayContent);
        ajaxmxc();
        return false;
    })
    //-- Get today's content
    if(document.getElementById("mxctodaylnk") != null)
    $.get(document.getElementById("mxctodaylnk").href+"&imajax=1", {},
       function(data){
         todayContent = data;
       });
    ajaxmxc();
    
});

