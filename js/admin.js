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
});;

function editRecord(e){
    alert("You pressed edit on row: " + $(this).data("row-id"));
}

function deleteRecord(e){
    alert("You pressed delete on row: " + $(this).data("row-id"));
}

function addRecord(){
    alert("You pressed add");
}
