$(function(){
    $('#search_section').change(function(){
        $(window).focus();
        var k='sec-' + $(this).val() + 'bo'
            ,c = SYS.cache(k)
            ,func = function(list){
                $('#search_board').empty().append(_.reduce(list, function(ret, item){
                    ret += '<option value="' + item.name + '">' + item.desc + '</option>';
                return ret;
                },''));
        };
        if(c){func(c);return;}
        var data = {root:'sec-' + $(this).val(), uid:SESSION.get('id'), bo:1};
        $.getJSON(SYS.ajax.section_list, data, function(list){
            SYS.cache(k,list);
            func(list);
        });
    }).change();    

    $('#search_form').submit(function(){
        BODY.open($(this).attr('action') + '?' + _.map($(this).getPostData(),function(v,k){
            v = encodeURIComponent(encodeURIComponent(v));
            return k + '=' + v;
        }).join('&'));  
        return false;
    });
});

