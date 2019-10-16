<?php
header("Content-type:application/javascript");
chdir(dirname(__FILE__) . '/../../');
include_once("./config.php");
include_once("./lib/loader.php");
include_once("./load_settings.php");

if (file_exists(ROOT.'languages/app_openweather_'.SETTINGS_SITE_LANGUAGE.'.php')) {
    include_once(ROOT.'languages/app_openweather_'.SETTINGS_SITE_LANGUAGE.'.php');
}
if (file_exists(ROOT.'languages/app_openweather_default.php')) {
    include_once(ROOT.'languages/app_openweather_default.php');
}

?>

(function()
{

    freeboard.loadWidgetPlugin({
        // Same stuff here as with datasource plugin.
        "type_name"   : "openweather_plugin",
        "display_name": "OpenWeatherMap",
        "description" : "OpenWeatherMap plugin",
        "fill_size" : true,
        "settings"    : [
            {
                "name"        : "widget_type",
                "display_name": LANG_TYPE,
                "type"        : "option",
                "options"     : [
                    {"name" : "<?php echo LANG_OW_WEATHER_NOW;?>","value": "1"},
                    {"name" : "<?php echo LANG_OW_FORECAST_FOR_SEVERAL_DAYS;?>","value": "2"},
                ]
            }
        ],
// Same as with datasource plugin, but there is no updateCallback parameter in this case.
        newInstance   : function(settings, newInstanceCallback)
        {
            newInstanceCallback(new myOWMPlugin(settings));
        }
    });

    var myOWMPlugin = function(settings)
    {
        var self = this;
        var currentSettings = settings;
        var widgetElement;
        function updateWidgetFrame()
        {
            if(widgetElement)
            {
                var widgetType=parseInt(currentSettings.widget_type);
                var newHeight=2*80-20;
                if (widgetType==1) {
                    newHeight=3*80-20;
                }
                if (widgetType==2) {
                    newHeight=5*80-20;
                }
                var myTextElement = $("<iframe style='margin-top:20px;height:"+newHeight+"px' src='<?php echo ROOTHTML;?>popup/app_openweather.html?widget_type="+widgetType+"<?php if ($_GET['theme']!='') echo "&theme=".$_GET['theme']; ?>&<?php echo $_GET['theme'];?>' width='100%' height='"+newHeight+"' frameborder=0></iframe>");
                $(widgetElement).append(myTextElement);
            }
        }

        self.render = function(element)
        {
            widgetElement = element;
            updateWidgetFrame();
        }

        self.getHeight = function()
        {
            var height = 2;
            var widgetType=parseInt(currentSettings.widget_type);
            if (widgetType==1) {
                height=3;
            }
            if (widgetType==2) {
                height=5;
            }
            return height;
        }

        self.onSettingsChanged = function(newSettings)
        {
            currentSettings = newSettings;
            updateWidgetFrame();
        }

        self.onCalculatedValueChanged = function(settingName, newValue)
        {
            updateWidgetFrame();
        }

        self.onDispose = function()
        {
        }

    }


}());
