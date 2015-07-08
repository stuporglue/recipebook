var ta = $('.searchbox').typeahead({
    highlight: false,
    hint: true,
    minLength: 3
},
{
    name: 'searcher',
    displayKey: 'plainlabel',
    source: function(query,cb){
        $.getJSON(relpath + 'query.php?q=' + encodeURIComponent(query),cb); 
        $('.tt-dataset-searcher').scrollTop(0);
        $('.tt-dropdown-menu').scrollTop(0);
    },
    templates: {
        empty: "<div class='empty-message'>This cook doesn't know what to make of that.</div>",
        // kind, label, search,url
        suggestion: Handlebars.compile("<div class='searchsuggestion {{kind}}'><h2><span class='kind'></span>{{{label}}}</h2><p class='wherefound'>{{{search}}}</p></div>")
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
$('.tt-dropdown-menu').css('max-height',$(window).height() - 50 + 'px')
$(window).on('resize',function(){
    var newh = $(window).height() - 50;
    if(newh < 100){
        newh = 100;
    }
    $('.tt-dropdown-menu').css('max-height',newh + 'px')
});

$('.sitesearch').on('submit',function(e){
    var searchVal = $(e.target).find('input')[1].value;
    if(searchVal.length > 0){
        $(e.target).find('.searchval').val(searchVal);
        var f = e.target
        f.submit();
        return true;
    }else{
        return false;
    }
});

if (window.navigator.userAgent.match(/iPad/i) || window.navigator.userAgent.match(/iPhone/i)) {
    // http://stackoverflow.com/questions/8057485/positionfixed-in-ios5-moves-when-input-is-focused
    $('.navbar').css('position','absolute');
    $('.navbar').css('top','0px');
    $('.smallsearch').css('position','absolute');
    $('.smallsearch').css('top','8px');
}

/*
 * What was this for? IOS bug maybe?
$('a[href^=#]').on('click',function(e){
    window.document.location = e.target.href;
    var distToTop = $(e.target.href.replace(/.*#/,'#')).offset().top - $(window).scrollTop();
    if(distToTop < 50){
        $(window).scrollTop($(window).scrollTop() - 50);
    }
    e.preventDefault();
    return false;
});
*/

