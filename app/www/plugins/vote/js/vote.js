$(function(){
    //for vote add
    if($('#vote_opt').length>0){
        var vote = {
            head:"#vote_head",
            items:"#vote_item",
            opt:"#vote_opt",
            templete:"",
            maxItem:20,
            itemNum:0,
            init:function(num){
                num = (num >this.maxItem)?this.maxItem:num;
                num = (num < 2)?2:num;
                this.templete = $(this.items).html();
                $(this.items).empty().show();
                for(var i=1;i<=num;i++)
                    this.addItem();
                $(this.items).on('click', 'samp', this.removeItem);
                $(this.opt).on('click', '.item-more', this.addItem);
            },
            addItem:function(){
                if(vote.itemNum >= vote.maxItem){
                    DIALOG.alertDialog("最多只能添加20个选项");
                    return false;
                }
                var name = "i" + Math.round(Math.random()*100000);
                $(vote.items).append(vote.templete.replace(/%name%/, name));
                vote.itemNum ++;
            },
            removeItem:function(){
                if(!confirm("确认删除此项?"))
                    return false;
                if(vote.itemNum <= 2){
                    DIALOG.alertDialog("至少保留2个选项");
                    return false;
                }
                $(this).parent().prev().remove();
                $(this).parent().remove();
                vote.itemNum --;
            },
            isNull:function(){
                if($.trim($(this.head).find('input').val()) == "")
                        return true;
                var iNull = 0;
                $(this.items).find('input').each(function(){
                    if($.trim($(this).val()) != ""){
                        iNull++;
                    }
                });
                return (iNull<2);
            }
        };
        vote.init(6);

        $('#f_vote').submit(function(){
            if(vote.isNull()){
                DIALOG.alertDialog('请至少填写标题和2个选项');
                return false;
            }
            if(!$('#vote_opt').find('input[name="end"]').val().match(/^\d{4}(-\d{2}){2}$/)){
                DIALOG.alertDialog('请填写正确的截止日期');
                return false;
            }
            $.post($(this).attr('action'), $(this).getPostData(), function(json){
                DIALOG.ajaxDialog(json)
            }, 'json');
            return false;
        });

        $('#vote_opt').find('input[name="end"]').datepicker({dateFormat:"yy-mm-dd",showMonthAfterYear:true,minDate:new Date(), dayNamesMin:['日', '一', '二', '三', '四', '五', '六'],monthNames:['一月', '二月', '三月', '四月', '五月', '六月','七月','八月','九月','十月','十一月','十二月']});

        $('#vote_section').change(function(){
            $(window).focus();
            var k='sec-' + $(this).val() + 'bo'
                ,c = SYS.cache(k)
                ,func = function(list){
                    $('#vote_board').empty()
                    .append(_.reduce(list, function(ret, item){
                        ret += '<option value="' + item.name + '">' + item.desc + '</option>';
                        return ret;
                    },'<option value="">请选择版面</option>'));
            };
            if(c){func(c);return;}
            var data = {root:'sec-' + $(this).val(), uid:SESSION.get('id'), bo:1};
            $.getJSON(SYS.ajax.section_list, data, function(list){
                SYS.cache(k,list);
                func(list);
            });
        }).change();
    }
    if($('#vote_table').length>0){
        var checkbox = $('#vote_table input:checkbox'),
            limit = $('#vote_table').attr('_limit');
        if(checkbox.length > 0 && limit > 0){
            checkbox.click(function(){
                if(checkbox.filter(':checked').length > limit){
                    DIALOG.alertDialog("本投票最多选"+limit+"个选项");
                    return false;
                }
            });
        }
        $('.vote-scroll span').each(function(){
            var w = parseInt($(this).attr('_width')) * 230 / 100;
            if(0 != w) $(this).animate({width:w + 'px'}, 1500);
        });
        $('#f_view').submit(function(){
            $.post($(this).attr('action'), $(this).getPostData(), function(json){
                if(json.ajax_st == 1)
                    BODY.refresh();
                else
                    DIALOG.ajaxDialog(json)
            }, 'json');
            return false;
        });
    }
    if($('#vote_share').length>0){
        BShare.init($('#vote_share'), $('#vote_share').attr('_u'), $('#vote_share').attr('_c'));
    }
    $('#body').on('click', '.vote-delete', function(){
        var self = this;
        DIALOG.confirmDialog('确认删除此投票?', function(){
            $.post($(self).attr('href'), function(json){
                if(json.ajax_st ==1){
                    if(BODY.get('path').match(/vote\/view\/\d+/))
                        BODY.open('vote?c=list&u=' + SESSION.get('id'));
                    else
                        BODY.refresh();
                }else{
                    DIALOG.ajaxDialog(json)
                }
            }, 'json');
        })
    });
    $('#vote_rank .vote-search input[placeholder]').placeholder();
    $('#vote_rank .vote-search form').submit(function(){
        BODY.open($(this).attr('action') + '?' + _.map($(this).getPostData(),function(v,k){
            v = encodeURIComponent(encodeURIComponent(v));
            return k + '=' + v;
        }).join('&'));
        return false;
    });
});
