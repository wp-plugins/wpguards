<?php
/**
 * The file that allow to connect API
 * 
 * @link         http://wpguards.com
 * @author       wpguards.com
 *
 * @package      WPGuards
 * @subpackage   WPGuards/includes
 */

class WPGuards_Curl
{
    /**
     * Fetch function. Inits curl connection, gets and decode response
     * 
     * @access public
     * @static
     * 
     * @param  string  $action   API action
     * @param  array   $request  request data.
     * @return array   $return   Response array
     */
    public static function fetch($action, array $request = array())
    {
        $options = get_option('wpguards_settings');

        if (isset($options['crm_apikey'])) {
            $pass = $options['crm_apikey'];
        } else {
            $pass = '';
        }

        $requestInfo = array(
            'home_url'       => home_url(),
            'client_version' => WPGUARDS_VERSION
        );

        $url = API_URL . '/api/v1/' . $action;

        // generate json encoded request
        $request = json_encode(array_merge($request, $requestInfo));

        // init curl connection
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        
        // login using API key
        curl_setopt($ch, CURLOPT_USERPWD, 'deprecated:' . $pass);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');

        // pass request data
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);

        // and get response
        $response = curl_exec($ch);
        curl_close ($ch);
        // decode and return
        
        $response = json_decode($response);

        if (!isset($response->status)) {
            $response = new stdClass;
            $response->status  = 'error';
            $response->message = __('Couldn\'t connect to API', 'wpguards');
        }

        return $response;
    }
}