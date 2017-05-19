/** notification toast  (hides after 3s ) */
$(document).ready(function() {
    setTimeout(function() {
        $('#snackbar').toggleClass("show", " ");
    }, 3000);
});

/* alert confirmation on delete edit etc add .confirmation class to
 * any object to proced */
$('.confirmation').click(function(e) {
    if (!confirm('Are you sure?')) e.preventDefault();
});

/* to highlight the search table
    function addHighlighting(element, textToHighlight) {
        var text = element.text().toLowerCase().trim();
        var highlightedText = '<span class="highlight">' + textToHighlight + '</span>';
        var newText = text.replace(textToHighlight, highlightedText);
        element.html(newText);
    }
 */
$(document).ready(function() {
    $("#searchtable").keyup(function() {
        var value = this.value.toLowerCase().trim();
        $("table.tabletosearch tr").each(function(index) {
            if (!index) return;
            $(this).find("td").not(".noindex").each(function() {
                var id = $(this).text().toLowerCase().trim();
                var not_found = (id.indexOf(value) == -1);
                $(this).closest('tr').toggle(!not_found);
                // addHighlighting($(this), value);
                return not_found;
            });
        });
    });
});
 