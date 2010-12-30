$(function(){
    update($('#s_section option:selected').val());
    $('#s_section').change(function(){
        $(window).focus();
        update($(this).find('option:selected').val());    
    });    

    function update(sec){
        var url = config.base + config.wsapi.s_boards + '?num=' + sec;
        $.getJSON(url,function(json){
            if(json.st == "success"){
                json = json.v;
                if(json.length > 0){
                    var append = "";
                    append += '<option value="' + json[0].bid + '" selected="true">' + json[0].name + '</option>';
                    for(var i = 1; i <= json.length - 1; i++){
                        append += '<option value="' + json[i].bid + '">' + json[i].name + '</option>';
                    }
                    $('#s_board').empty().append(append);
                }
            }
        });
    }
});

