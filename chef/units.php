<?php

session_start();
if($_SESSION['loggedin'] !== TRUE){
    header("Location:index.php");
    exit();
}

require_once('../lib/template.php');

printHeader("Manage Units");
?>
<div class="jumbotron">
<h1>Manage Units</h1>
</div>
<div>
    <table id="grid-data" data-type='units' class="table table-condensed table-hover table-striped">
        <thead>
            <tr>
                <th data-css='slimmer' data-column-id="commands" data-formatter="commands" data-sortable="false">Edit</th>
                <th data-column-id="id" data-order="asc" data-identifier="true" data-type="numeric">ID</th>
                <th data-column-id="name">Name</th>
                <th data-column-id="plural">Plural</th>
                <th data-column-id="base_unit">Base Unit</th>
                <th data-column-id="base_count" data-type="numeric">Base Unit Count</th>
            </tr>
        </thead>
    </table>
</div>
<?php
printFooter();

