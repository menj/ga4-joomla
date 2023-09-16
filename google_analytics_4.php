<?php
defined('_JEXEC') or die;

class plgSystemGoogle_Analytics_4 extends JPlugin
{
    public function onBeforeCompileHead()
    {
        try {
            $doc = JFactory::getDocument();
            if ($doc->getType() !== 'html') {
                return;
            }

            $measurementId = $this->params->get('measurement_id', '');
            
            // Validate the format of the Measurement ID
            if (!preg_match('/^G-[a-zA-Z0-9]+$/', $measurementId)) {
                throw new Exception('Invalid Measurement ID format.');
            }

            $script = <<<EOL
            <!-- Google tag (gtag.js) -->
            <script async src="https://www.googletagmanager.com/gtag/js?id={$measurementId}"></script>
            <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{$measurementId}');
            </script>
            EOL;

            $doc->addCustomTag($script);
        } catch (Exception $e) {
            // Log the error message
            JLog::add($e->getMessage(), JLog::ERROR, 'plg_system_google_analytics_4');
            
            // Add a warning message to Joomla's system message queue
            JFactory::getApplication()->enqueueMessage('Google Analytics 4 Plugin: ' . $e->getMessage(), 'warning');
        }
    }
}
