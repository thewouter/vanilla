<?php if (!defined('APPLICATION')) exit();
$Session = Gdn::session();
$UpdateUrl = c('Garden.UpdateCheckUrl');
$AddonUrl = c('Garden.AddonUrl');
$PluginCount = count($this->AvailablePlugins);
$EnabledCount = count($this->EnabledPlugins);
$DisabledCount = $PluginCount - $EnabledCount;
?>
<script type="text/javascript">
    jQuery(document).ready(function($) {
        var selectors = '.plugins-all, .plugins-enabled, .plugins-disabled';
        $(selectors).click(function() {
            $(selectors).parents('li').removeClass('Active');
            $(this).parents('li').addClass('Active');
            if ($(this).hasClass('plugins-disabled')) {
                $('li.Enabled').hide();
                $('li.Disabled').show();
            } else if ($(this).hasClass('plugins-enabled')) {
                $('li.Enabled').show();
                $('li.Disabled').hide();
            } else {
                $('li.Enabled').show();
                $('li.Disabled').show();
            }
            return false;
        });
    });
</script>
<h1><?php echo t('Manage Plugins'); ?></h1>
<div class="Info">
    <?php
    printf(
        t('PluginHelp'),
        '<code>'.PATH_PLUGINS.'</code>'
    );
    ?>
</div>
<div class="Tabs FilterTabs">
    <ul>
        <li<?php echo $this->Filter == 'all' ? ' class="Active"' : ''; ?>><?php echo anchor(sprintf(t('All %1$s'), wrap($PluginCount)), 'settings/plugins/all', 'plugins-all'); ?></li>
        <li<?php echo $this->Filter == 'enabled' ? ' class="Active"' : ''; ?>><?php echo anchor(sprintf(t('Enabled %1$s'), wrap($EnabledCount)), 'settings/plugins/enabled', 'plugins-enabled'); ?></li>
        <li<?php echo $this->Filter == 'disabled' ? ' class="Active"' : ''; ?>><?php echo anchor(sprintf(t('Disabled %1$s'), wrap($DisabledCount)), 'settings/plugins/disabled', 'plugins-disabled'); ?></li>
    </ul>
</div>
<?php echo $this->Form->errors(); ?>
<div class="Messages Errors TestAddonErrors Hidden">
    <ul>
        <li><?php echo t('The addon could not be enabled because it generated a fatal error: <pre>%s</pre>'); ?></li>
    </ul>
</div>
<ul class="data-list">
    <?php
    $Alt = FALSE;
    foreach ($this->AvailablePlugins as $PluginName => $PluginInfo) {
        // Skip Hidden & Trigger plugins
        if (isset($PluginInfo['Hidden']) && $PluginInfo['Hidden'] === TRUE)
            continue;
        if (isset($PluginInfo['Trigger']) && $PluginInfo['Trigger'] == TRUE) // Any 'true' value.
            continue;

        $Css = array_key_exists($PluginName, $this->EnabledPlugins) ? 'Enabled' : 'Disabled';
        $State = strtolower($Css);
        if ($this->Filter == 'all' || $this->Filter == $State) {
            $Alt = $Alt ? FALSE : TRUE;
            $Version = Gdn_Format::Display(val('Version', $PluginInfo, ''));
            $ScreenName = Gdn_Format::Display(val('Name', $PluginInfo, $PluginName));
            $SettingsUrl = $State == 'enabled' ? val('SettingsUrl', $PluginInfo, '') : '';
            $PluginUrl = val('PluginUrl', $PluginInfo, '');
            $Author = val('Author', $PluginInfo, '');
            $AuthorUrl = val('AuthorUrl', $PluginInfo, '');
            $NewVersion = val('NewVersion', $PluginInfo, '');
            $Upgrade = $NewVersion != '' && version_compare($NewVersion, $Version, '>');
            $RowClass = $Css;
            if ($Alt) $RowClass .= ' Alt';
            $IconPath = '/plugins/'.GetValue('Folder', $PluginInfo, '').'/icon.png';
            $IconPath = file_exists(PATH_ROOT.$IconPath) ? $IconPath : 'applications/dashboard/design/images/plugin-icon.png';
            ?>
            <li <?php echo 'id="'.Gdn_Format::url(strtolower($PluginName)).'-plugin"', ' class="item addon-item More '.$RowClass.'"'; ?>>
                <div class="addon-icon">
                    <div class="icon-wrap"><?php echo img($IconPath, array('class' => 'PluginIcon')); ?></div>
                </div>
                <div class="addon-body">
                    <div class="addon-name"><?php echo $ScreenName; ?></div>
                    <div class="meta"><?php
                        $RequiredApplications = val('RequiredApplications', $PluginInfo, false);
                        $RequiredPlugins = val('RequiredPlugins', $PluginInfo, false);
                        $Info = '';

                        if ($Author != '') {
                            $Info .= sprintf(t('By %s'), $AuthorUrl != '' ? anchor($Author, $AuthorUrl) : $Author);
                        }

                        if ($Version != '') {
                            $Info .= ' <span>·</span> ';
                            $Info .= sprintf(t('Version %s'), $Version);
                        }

                        if (is_array($RequiredApplications) || is_array($RequiredPlugins)) {
                            if ($Info != '')
                                $Info .= ' <span>·</span> ';

                            $Info .= t('Requires: ');
                        }

                        $i = 0;
                        if (is_array($RequiredApplications)) {
                            if ($i > 0)
                                $Info .= ', ';

                            foreach ($RequiredApplications as $RequiredApplication => $VersionInfo) {
                                $Info .= sprintf(t('%1$s Version %2$s'), $RequiredApplication, $VersionInfo);
                                ++$i;
                            }
                        }

                        if ($RequiredPlugins !== FALSE) {
                            foreach ($RequiredPlugins as $RequiredPlugin => $VersionInfo) {
                                if ($i > 0)
                                    $Info .= ', ';

                                $Info .= sprintf(t('%1$s Version %2$s'), $RequiredPlugin, $VersionInfo);
                                ++$i;
                            }
                        }

                        if ($PluginUrl != '') {
                            $Info .= ' <span>·</span> ';
                            $Info .= anchor(t('Visit Site'), $PluginUrl);
                        }

                        echo $Info != '' ? $Info : '&#160;';

                        ?></div>
                    <div class="addon-description">
                        <?php
                        echo Gdn_Format::Html(t(val('Name', $PluginInfo, $PluginName).' Description', val('Description', $PluginInfo, '')));
                        ?>
                    </div>
                </div>
                <div class="addon-settings">
                    <?php
                    if ($SettingsUrl != '') {
                        echo anchor(t('Settings'), $SettingsUrl, 'addon-settings-link');
                    }
                    ?>
                </div>
                <div class="addon-enable">
                    <?php
                    $ToggleText = array_key_exists($PluginName, $this->EnabledPlugins) ? 'Disable' : 'Enable';
                    echo anchor(
                        t($ToggleText),
                        '/settings/plugins/'.$this->Filter.'/'.$PluginName.'/'.$Session->TransientKey(),
                        $ToggleText.'Addon SmallButton'
                    );
                    ?>
                </div>
            </li>
            <?php
        }
    }
    ?>
</ul>
