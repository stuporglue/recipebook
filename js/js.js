var ta = $('.searchbox').typeahead({
    highlight: false,
    hint: true,
    minLength: 3
},
{
    name: 'searcher',
    displayKey: 'label',
    source: function(query,cb){
        $.getJSON(relpath + 'search.php?q=' + encodeURIComponent(query),cb); 
        $('.tt-dataset-searcher').scrollTop(0);
        $('.tt-dropdown-menu').scrollTop(0);
    },
    templates: {
        empty: "<div class='empty-message'>This cook doesn't know what to make of that.</div>",
        // kind, label, search,url
        suggestion: Handlebars.compile("<div class='searchsuggestion {{kind}}'><h2>{{{label}}}</h2><p class='wherefound'>{{{search}}}</p></div>")
    }
});

ta.on('typeahead:closed', function(){
    $('.tt-dataset-searcher').scrollTop(0);
    $('.tt-dropdown-menu').scrollTop(0);
});

$('.searchbox').bind('typeahead:selected', function(obj, datum, name) { 
    window.location.href = relpath + datum.url;
});

$('.typeahead').typeahead('val', '');
