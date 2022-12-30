<?php

function send_request($url, $data=false) 
{
    $target_url = curl_init($url);

    if ($data) curl_setopt($target_url, CURLOPT_POSTFIELDS, $data);
    
    curl_setopt($target_url, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($target_url, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($target_url, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($target_url, CURLOPT_FOLLOWLOCATION, true);

    $target_content = curl_exec($target_url);
    return $target_content;
}

class telegraph 
{
    public static $access_token;

    function __construct($token) {
        self::$access_token = $token;
    }

    function create_page($page_title, $page_content) {
        $page_content = '[{"tag":"p","children":["'.$page_content.'"]}]';

        $create_page_query = array(
            'access_token'   => self::$access_token,
            'title'          => $page_title,
            'content'        => $page_content,
            'return_content' => 'true'
        );

        $telegraph_query = send_request('https://api.telegra.ph/createPage?' . http_build_query($create_page_query));
        return $telegraph_query;
    }

    function show_page() {
        $create_page_query = array('access_token' => self::$access_token);

        $telegraph_query = send_request('https://api.telegra.ph/getPageList?' . http_build_query($create_page_query));
        $telegraph_query_decode = json_decode($telegraph_query, true);

        $post_nums = $telegraph_query_decode['result']['pages'];
        $post_material = "";
        
        foreach ($post_nums as $post_num) {
            $post_material .= "\n";

            foreach ($post_num as $post_num_elem => $post_num_content) {
                $post_material .= "$post_num_elem: $post_num_content\n";
            }
        };

        return $post_material;
    }

}

$myToken = "01db0643501fb131336ba3f41fbae564f933e9a2f8e90f80c60d37bae95e";

$tphManager = new telegraph($myToken);

$myPages_json = $tphManager->show_page();
print_r($myPages_json);

?>
