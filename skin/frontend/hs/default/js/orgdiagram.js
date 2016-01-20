jQuery.noConflict();
jQuery(function ($) {    
    var options = new primitives.orgdiagram.Config();
    
    options.items = items;
    options.cursorItem = 0;
    options.templates = [getContactTemplate()];
    options.defaultTemplateName = "contactTemplate";    
    options.onItemRender = onTemplateRender;
    options.hasButtons = primitives.common.Enabled.True;
    options.hasSelectorCheckbox = primitives.common.Enabled.False;
    options.onButtonClick = function (e, data) {
        var message = "User clicked '" + data.name + "' button for item '" + data.context.title + "'.";
        alert(message);
    };
   
    
    $('#basicdiagram').orgDiagram(options);
    $('#basicdiagram').orgDiagram("update",primitives.common.UpdateMode.Refresh);
    
    $('input[name="fit"]').change(function() {
        options.pageFitMode = $(this).is(':checked') ? 1 : 0;
        $('#basicdiagram').orgDiagram(options);
        $('#basicdiagram').orgDiagram("update", primitives.common.UpdateMode.Refresh);                    
    });
    
    $('input[name="options"]').change(function() {
        if ($(this).val() == 1)
            options.items = items;
//        else if ($(this).val() == 2)
//            options.items = colocados;
        else if ($(this).val() == 3)
            options.items = frontales;
//        else if ($(this).val() == 4)
//            options.items = preferentes;
        
        
        $('#basicdiagram').orgDiagram(options);
        $('#basicdiagram').orgDiagram("update", primitives.common.UpdateMode.Refresh);                    
    });
    
    
    function getContactTemplate() {
        var result = new primitives.orgdiagram.TemplateConfig();
        result.name = "contactTemplate";

        var buttons = [];
        buttons.push(new primitives.orgdiagram.ButtonConfig("revert", "ui-icon-transferthick-e-w", "Revert"));
        buttons.push(new primitives.orgdiagram.ButtonConfig("email", "ui-icon-mail-closed", "E-Mail"));
        buttons.push(new primitives.orgdiagram.ButtonConfig("help", "ui-icon-help", "Help"));

        result.buttons = buttons;

        result.itemSize = new primitives.common.Size(220, 140);
        result.minimizedItemSize = new primitives.common.Size(3, 3);
        result.highlightPadding = new primitives.common.Thickness(2, 2, 2, 2);

        var itemTemplate = jQuery(
          '<div class="bp-item bp-corner-all bt-item-frame">'
            + '<div name="titleBackground" class="bp-item bp-corner-all bp-title-frame" style="top: 2px; left: 2px; width: 216px; height: 20px;">'
                + '<div name="title" class="bp-item bp-title" style="top: 3px; left: 6px; width: 208px; height: 18px;">'
                + '</div>'
            + '</div>'
            + '<div class="bp-item bp-photo-frame" style="top: 26px; left: 164px; width: 50px; height: 60px;">'
                + '<img name="photo" style="height:60px; width:50px;" />'
            + '</div>'
            + '<div name="rank" class="bp-item" style="top: 26px; left: 6px; width: 162px; height: 36px; font-size: 12px;"></div>'                    
            + '<div name="code" class="bp-item" style="top: 48px; left: 6px; width: 162px; height: 36px; font-size: 12px;"></div>'                    
            + '<div name="vp" class="bp-item" style="top: 70px; left: 6px; width: 162px; height: 18px; font-size: 10px;">VP: </div>'
            + '<div name="vg" class="bp-item" style="top: 86px; left: 6px; width: 162px; height: 18px; font-size: 10px;"></div>'
            + '<div name="percentage" class="bp-item" style="top: 116px; left: 6px; width: 162px; height: 18px; font-size: 12px; color: green; font-weight: bold;"></div>'
            + '<div name="sponsor_name" class="bp-item" style="font-weight: bold; top: 136px; left: 6px; width: 162px; height: 18px; font-size: 12px;"></div>'
            + '<div name="referral" class="bp-item" style="top: 150px; left: 6px; width: 162px; height: 18px; font-size: 12px;"></div>'
            
        + '</div>'
        ).css({
            width: result.itemSize.width + "px",
            height: result.itemSize.height + "px"
        }).addClass("bp-item bp-corner-all bt-item-frame");
        result.itemTemplate = itemTemplate.wrap('<div>').parent().html();

        return result;
    }
            
    function onTemplateRender(event, data) {
        switch (data.renderingMode) {
            case primitives.common.RenderingMode.Create:
                /* Initialize widgets here */
                break;
            case primitives.common.RenderingMode.Update:
                /* Update widgets here */
                break;
        }
        
        var itemConfig = data.context;
        
        data.element.find("[name=photo]").attr({ "src": itemConfig.image, "alt": itemConfig.title });
        data.element.find("[name=titleBackground]").css({ "background": itemConfig.itemTitleColor });

        var fields = ["title", "description", "rank"/*, "referral", "sponsor_name"*/, "vp","vg","percentage","code"];
        for (var index = 0; index < fields.length; index++) {
            var field = fields[index];

            var element = data.element.find("[name=" + field + "]");
            if (element.text() != itemConfig[field]) {
                if (field == "vp"){
                    element.text("VP: "  + itemConfig[field]);
                }else if (field == "vg"){
                    element.text("VG: "  + itemConfig[field]);
                }else{
                    element.text(itemConfig[field]);
                }
                
            }
        }
    }    
});

