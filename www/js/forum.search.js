$(function(){
    $('#body').on('change', '#search_section',function(){
        $(window).focus();
        APP.cacheSection($(this).val(), function(list){
            $('#search_board').empty().append(_.reduce(list, function(ret, item){
                ret += '<option value="' + item.name + '">' + item.desc + '(' + item.name + ')</option>';
                return ret;
            },''));
        }, this);
    }).change();
    APP.cacheSection('root', function(list){
        $('#search_section').empty().append(_.reduce(list, function(ret, item){
            ret += '<option value="' + item.name + '">' + item.name + 'Çø:'+ item.desc + '</option>';
            return ret;
        },'')).change();
    }, this);

    $('#search_form').submit(function(){
        BODY.open($(this).attr('action') + '?' + _.map($(this).getPostData(),function(v,k){
            v = encodeURIComponent(encodeURIComponent(v));
            return k + '=' + v;
        }).join('&'));
        return false;
    });
});

