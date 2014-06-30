(function ($) {
    "use strict";

    $(function () {

        var components = {
            "key": "Sidebars",
            "views": [
                {
                    "component": {
                        "id": "register-sidebar",
                        "label": "New Widget Area",
                        "description": "Lorem ipsum dolor sit amet, consectetur adipisicing elit. Deserunt, ipsum?"
                    },
                    "options": {
                        "type": "shortcode",
                        "shortcode_id": "register_sidebar",
                        "shortcode_atts": ["id"],
                        "data": {
                            "id": "1"
                        },
                        "form": [
                            {
                                "name": "id",
                                "label": "Sidebar ID",
                                "type": "input"
                            }
                        ]
                    }
                },
                {
                    "component": {
                        "id": "display-sidebar",
                        "label": "Existing Sidebar",
                        "description": "Lorem ipsum dolor sit amet, consectetur adipisicing elit. Deserunt, ipsum?"
                    },
                    "options": {
                        "type": "shortcode",
                        "shortcode_atts": ["id"],
                        "shortcode_id": "display_sidebar",
                        "data": {
                            "id": "my-sidebar"
                        },
                        "form": [
                            {
                                "name": "id",
                                "label": "Sidebar ID",
                                "type": "input"
                            }
                        ]
                    }
                }
            ]
        };

        $(document).on('loaded.ComponentsCtrl.builder', function()
        {
            var Components = angular.element('[ng-controller="ComponentsCtrl"]').scope();
            if (typeof Components !== 'undefined' && typeof Components.components !== 'undefined')
                Components.after(components, 'Grid');
        });

    });

})(jQuery);