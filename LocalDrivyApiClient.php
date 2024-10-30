<?php
class LocalDrivyApiClient
{
    /**
     * Return api localdrivy base url
     *
     * @return void
     */
    public function getDomain()
    {
        $domain = 'https://back.localdrivy.fr/';
        return $domain;
    }

    private function formatUrl($url)
    {
        $url = urldecode($url);
        $url =    str_replace(' ', '%20', $url);
        $url =    str_replace('\\', '', $url);
        $url =    str_replace('\'', '"', $url);
        return $url;
    }

        
    /**
     * callGet
     *
     * @param  mixed $url
     * @param  mixed $headers
     * @return void
     */
    public function callGet($url, $headers = array())
    {
        $url = remove_accents($url);
        $url = $this->getDomain() . $url;
        $apikey = get_option('localdrivy_plugin_options')['api_key'];
        $headers['LcApikey'] =  $apikey;
        $url = $this->formatUrl($url);
        $request = wp_remote_get($url, array(
            'headers' => $headers
        ));
        $body     = wp_remote_retrieve_body($request);
        return $body;
    }
    
    /**
     * callDelete
     *
     * @param  mixed $url
     * @param  mixed $headers
     * @return void
     */
    public function callDelete($url, $headers = array())
    {
        $url = remove_accents($url);

        $url = $this->getDomain() . $url;
        $apikey = get_option('localdrivy_plugin_options')['api_key'];
        $headers['LcApikey'] =  $apikey;
        $url = $this->formatUrl($url);
        $request = wp_remote_request($url, array(
            'method' => 'DELETE',
            'headers' => $headers
        ));
        $body     = wp_remote_retrieve_body($request);
        return $body;
    }

    
    /**
     * callPost
     *
     * @param  mixed $url
     * @param  mixed $datas
     * @param  mixed $headers
     * @return void
     */
    public function callPost($url, $datas, $headers = array())
    {
        $url = remove_accents($url);
        if (is_array($datas)) {
            $datas = json_encode($datas);
        }

        $url = $this->getDomain() . $url;
        $apikey = get_option('localdrivy_plugin_options')['api_key'];
        $headers['LcApikey'] = $apikey;
        $headers['Content-Type'] = 'application/json';
        $url = $this->formatUrl($url);
        $request = wp_remote_post($url, array(
            'headers' => $headers,
            'body'=>$datas
        ));
        $body     = wp_remote_retrieve_body($request);
        return $body;
    }

    
    /**
     * callGetJson
     *
     * @param  mixed $url
     * @return void
     */
    public function callGetJson($url)
    {
        $response = $this->callGet($url);
        json_decode($response, true);
    }
    
    /**
     * callGetHtml
     *
     * @param  mixed $url
     * @return void
     */
    public function callGetHtml($url)
    {
        $response = $this->callGet($url);
        return $response;
    }
    /**
     * getScheduleInfos
     *
     * @return void
     */
    public function getScheduleInfos()
    {
        $url = 'api/public/wp/scheduleinfos';
        $html = $this->callGetHtml($url);
        return $html;
    }
    /**
     * getDriveInfos
     *
     * @return void
     */
    public function getDriveInfos()
    {
        $url = 'api/public/wp/driveinfos';
        $html = $this->callGetHtml($url);
        return $html;
    }
    /**
     * getCategories
     *
     * @param  mixed $viewMode
     * @return void
     */
    public function getCategories($viewMode)
    {
        $filters = '{"categories":"","name":null}';
        if (isset($_GET['filters'])) {
            $filters = sanitize_text_field($_GET['filters']);
        }
        $currentpage = 1;
        if (isset($_GET['lcpage'])) {
            if (!is_numeric($_GET['lcpage'])) {
                $currentpage = 1;
            } else {
                $currentpage = (int) $_GET['lcpage'];
            }
        }

        $baseUrl = 'api/public/wp/categories?filters=';
        $url = $baseUrl . $filters . '&lcpage=' . $currentpage . '&lcviewmode=' . $viewMode;

        $html = $this->callGetHtml($url);
        return $html;
    }
    
    /**
     * getCartContent
     *
     * @return void
     */
    public function getCartContent()
    {
        $url = 'api/public/wp/getCartTemplate';
        $html = $this->callGetHtml($url);
        return $html;
    }
    
    /**
     * getProducts
     *
     * @return void
     */
    public function getProducts()
    {
        $filters = '{"categories":"","name":null}';
        if (isset($_GET['filters'])) {
            $filters = sanitize_text_field($_GET['filters']);
        }
        $currentpage = 1;
        if (isset($_GET['lcpage'])) {
            if (!is_numeric($_GET['lcpage'])) {
                $currentpage = 1;
            } else {
                $currentpage =  (int) $_GET['lcpage'];
            }
        }

        $baseUrl = 'api/public/wp/productshtml?filters=';
        $url = $baseUrl . $filters . '&lcpage=' . $currentpage;
        $html = $this->callGetHtml($url);
        return $html;
    }
    
    /**
     * addToCart
     *
     * @param  mixed $idProduct
     * @param  mixed $unit
     * @param  mixed $qty
     * @return void
     */
    public function addToCart($idProduct, $unit, $qty)
    {
        $headers = $this->getSessionHeaders();
        return  $this->callPost('api/public/merchantcart/addcartline', array('productId' => $idProduct, 'qtyUnit' => $unit, 'qty' => floatval($qty)), $headers);
    }
    
    /**
     * deleteCartLine
     *
     * @param  mixed $idLine
     * @return void
     */
    public function deleteCartLine($idLine)
    {
        $headers = $this->getSessionHeaders();
        return  $this->callDelete('api/public/merchantcart/delete/' . $idLine, $headers);
    }
    
    /**
     * getCart
     *
     * @return void
     */
    public function getCart()
    {
        $cartstr = $this->callGet('api/public/merchantcart/cart', $this->getSessionHeaders());
        return $cartstr;
    }
    
    /**
     * validOrder
     *
     * @return void
     */
    public function validOrder()
    {
        $baseUrl = home_url('');
        $data = ['backUrl' => $baseUrl];
        return $this->callPost('api/public/merchantcart/validorder', $data, $this->getSessionHeaders());
    }
    
    /**
     * getJs
     *
     * @return void
     */
    public function getJs()
    {
        $contenttmp = $this->callGet('api/public/wp/jsfile');
        $content = json_decode($contenttmp, true);
        return $content['js'];
    }
    /**
     * getCss
     *
     * @return void
     */
    public function getCss()
    {
        $contenttmp = $this->callGet('api/public/wp/cssfile');
        $content = json_decode($contenttmp, true);
        return $content['css'];
    }


    
    /**
     * getSessionHeaders
     *
     * @param  mixed $headers
     * @return void
     */
    private function getSessionHeaders($headers = array())
    {
        $sessionId = session_id();
        if ($sessionId == null) {
            session_start();
            $sessionId = session_id();
        }
        $headers[ 'external-sessionid'] = $sessionId;
        return $headers;
    }
}
