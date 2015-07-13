var grid = $("#grid-data").bootgrid({
    ajax: true,
    rowCount: 75,
    post: function ()
    {
        /* To accumulate custom parameter with the request object */
        return {
            table: $('#grid-data').data('type')
        };
    },
    url: './ajax.php',
    formatters: {
        "commands": function(column, row) {
            return "<button type=\"button\" class=\"btn btn-xs btn-default command-edit\" data-row-id=\"" + row.id + "\"><span class=\"fa fa-pencil\"></span></button> " + 
                "<button type=\"button\" class=\"btn btn-xs btn-default command-delete\" data-row-id=\"" + row.id + "\"><span class=\"fa fa-trash-o\"></span></button>";
        },
        "tags" : function(column,row){
            var tags = [];
            if(row.quick == 't'){
                tags.push("<span title='Quick' class='glyphicon glyphicon-time'></span>");
            }
            if(row.favorite == 't'){
                tags.push("<span title='Favorite' class='glyphicon glyphicon-heart'></span>");
            }
            if(row.hide == 't'){
                tags.push("<span title='Hidden' class='glyphicon glyphicon-eye-close'></span>");
            }
            return tags.join(' ');
        },
        "recipe-link" : function(column,row){
            return "<a onclick='return previewLink(this);' href=\"../recipe/" + encodeURI(row.name) + "\">" + $('<div>').text(row.name).html() + "</a>";
        }
    },
   templates: {
        header: "<div id=\"{{ctx.id}}\" class=\"{{css.header}}\">" + 
                    "<div class=\"row\">" + 
                        "<div class=\"col-sm-1 actionBar\">" + 
                            "<!--Your Button goes here-->" + 
                            "<button type='button' class='pull-left btn btn-md btn-default command-add'><span class='fa fa-plus-square'></span></button>" + 
                        "</div>" + 
                        "<div class=\"col-sm-11 actionBar\">" + 
                            "<p class=\"{{css.search}}\"></p>" + 
                            "<p class=\"{{css.actions}}\"></p>" + 
                        "</div>" + 
                    "</div>" + 
                "</div>"
    }
}).on("loaded.rs.jquery.bootgrid", function()
{
    /* Executes after data is loaded and rendered */
    grid.find(".command-edit").on("click", editRecord);
    grid.find(".command-delete").on("click",deleteRecord);
    $(".command-add").on("click",addRecord);
});


function previewLink(link){
    $('#myModal .modal-body').html('<iframe class="iframepreview" src="' + $(link).attr('href') + '"></iframe>');
    $('#myModalLabel').html('Preview of ' + $(link).text());
    $('#savebutton').hide();
    $('#myModal').modal('show');
    return false;
}

function editRecord(e){
    $.get($('#grid-data').data('type') + '_form.php?id=' + $(e.target).closest('tr').data('row-id'),function(res){
        $('#myModal .modal-body').html(res);
        $('#myModalLabel').html('Edit ' + $('#grid-data').data('type') + " #" + $(e.target).closest('tr').data('row-id'));
        $('#savebutton').show();
        $('#myModal').modal('show');
    });
}

function deleteRecord(e){
    var delme = confirm("Do you want to delete row " + $(e.target).closest('tr').data("row-id"));
    if(delme){
        var postUrl = $('#grid-data').data('type') + '_form.php';
        var postData = {
            'action' : 'delete',
            'id' : $(e.target).closest('tr').data('row-id')
        };

        $.post(postUrl,postData).then(function(success){
            grid.bootgrid('reload');
        },function(failure){
            console.log(failure);
        });
    }
}

function addRecord(){
    $.get($('#grid-data').data('type') + '_form.php',function(res){
        $('#myModal .modal-body').html(res);
        $('#myModalLabel').html('Add ' + $('#grid-data').data('type'));
        $('#myModal').modal('show');
    });
}

$('#savebutton').on('click', function(){
    var theForm = $('#myModal form');
    var postUrl = theForm.attr('action');
    var postData = theForm.serialize();

    $.post(postUrl,postData).then(function(success){
        console.log("Success");
        grid.bootgrid('reload');
        $('#myModal').modal('hide');
    },function(failure){
        console.log(failure);
    });
});

function setupModalHandlers(){
    $('[data-source]:not(.tt-hint):not(.tt-input)').each(function(a,b){
        b = $(b);
        var url = b.data('source');
        b.typeahead({},{
            name: b.data('source'),
            displayKey: 'name',
            source: function(query,cb){
                $.getJSON(relpath + 'chef/ta.php?t=' + b.data('source').replace('ta_','') + '&q=' + encodeURIComponent(query),cb);
            }
        });

        b.bind('typeahead:selected', function(obj, datum, name) { 
            $(obj.target).closest('td').find('input[type=hidden]').val(datum.id);
        });
    });

    tinymce.init({selector:'textarea'});
}

$('#myModal').on('shown.bs.modal',setupModalHandlers);

$('#myModal').on('change','tr.autonewrow input',function(e){
    var tr = $(e.target).closest('tr');
    tr.find('input').typeahead('close');
    var template = templates[tr.data('type')];
    if(templates !== undefined){
        tr.after(template);
    }
    setupModalHandlers();
    tr.removeClass('autonewrow');
});
