// https://github.com/twitter/typeahead.js/blob/master/doc/bloodhound.md
var searcher = new Bloodhound({
    datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
    queryTokenizer: Bloodhound.tokenizers.whitespace,
    remote: {
        url: relpath + 'search.php?q=%QUERY',
    }
});

searcher.initialize();

$('.searchbox').typeahead({
    highlight: true,
    hint: true,
    minLength: 3
},
{
    name: 'Searcher',
    displayKey: 'value',
    source: searcher.ttAdapter()
});
