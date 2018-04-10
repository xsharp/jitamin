Jitamin.Dropdown = function(app) {
    this.app = app;
};

Jitamin.Dropdown.prototype.listen = function() {
    var self = this;

    $(document).on('click', function() {
        self.close();
    });

    $(document).on('click', '.dropdown-menu', function(e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        self.close();

        var submenu = $(this).next('ul');
        var offset = $(this).offset();

        // Clone the submenu outside of the column to avoid clipping issue with overflow
        $("body").append(jQuery("<div>", {"id": "dropdown"}));
        submenu.clone().appendTo("#dropdown");

        var clone = $("#dropdown ul");
        clone.addClass('dropdown-submenu-open');

        var submenuHeight = clone.outerHeight();
        var submenuWidth = clone.outerWidth();

        if (offset.top + submenuHeight - $(window).scrollTop() < $(window).height() || $(window).scrollTop() + offset.top < submenuHeight) {
            if ($(this).parents('.sidebar').length) {
                clone.css('top', offset.top - 5);
            } else if($(this).parents('.navbar').length) {
                clone.css('top', offset.top + 15 + $(this).height());
            } else {
                clone.css('top', offset.top + $(this).height());
            }
        }
        else {
            clone.css('top', offset.top - submenuHeight - 5);
        }

        if (offset.left + submenuWidth > $(window).width()) {
            if($(this).parents('.navbar').length) {
                clone.css('left', offset.left - submenuWidth - 15 + $(this).outerWidth());
            } else {
                clone.css('left', offset.left - submenuWidth + $(this).outerWidth());
            }
        }
        else {
            if ($(this).parents('.sidebar').length) {
                 clone.css('left', $('.sidebar').width());
            } else {
                clone.css('left', offset.left);
            }
        }
    });

    $(document).on('click', '.dropdown-submenu-open li', function(e) {
        if ($(e.target).is('li')) {
            $(this).find('a:visible')[0].click(); // Calling native click() not the jQuery one
        }
    });
};

Jitamin.Dropdown.prototype.close = function() {
    $("#dropdown").remove();
};

Jitamin.Dropdown.prototype.onPopoverOpened = function() {
    this.close();
};
