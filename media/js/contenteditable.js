var contenteditable;
(function (contenteditable) {
    var $ = jQuery;
    function strFloatToFloat(t) {
        var i, v;
        i = t.indexOf(",");
        if(i !== -1) {
            v = parseFloat(t.substr(0, i) + "." + t.substr(i + 1));
        } else {
            v = parseFloat(t);
        }
        return v;
    }
    function content2value($t) {
        if($t.hasClass('is-number')) {
            if($t.hasClass("is-float")) {
                return strFloatToFloat($t.text());
            } else {
                return parseInt($t.text());
            }
        } else {
            return $t.text();
        }
    }
    $(document).on("focus", "[contenteditable]", function (event) {
        var $t = $(event.target);
        $t.data().value = content2value($t);
    });
    $(document).keypress(function (event) {
        var $t;
        if(event.which === 13 && typeof ($t = $(event.target)).attr("contenteditable") !== 'undefined') {
            $t.trigger("blur");
            return false;
        }
    });
    $(document).on("blur", "[contenteditable]", function (event) {
        var oldValue = $(this).data().value;
        var newValue = content2value($(this));
        $(this).trigger("change", [
            newValue, 
            oldValue
        ]);
    });
})(contenteditable || (contenteditable = {}));
