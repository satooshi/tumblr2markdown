<?php
namespace Contrib\Component\Tumblr2Markdown\ApiClient\V1;

class TumblrClient
{
    protected $blogName;

    public function __construct($blogName)
    {
        $this->blogName = $blogName;
    }

    /**
     * Call read API.
     *
     * @param array $query
     * @return \SimpleXMLElement
     */
    public function read(array $query)
    {
        $queryString = $this->buildQueryString($query);
        $url         = sprintf('http://%s.tumblr.com/api/read%s', $this->blogName, $queryString);
        $data        = $this->callApi($url);

        return new \SimpleXMLElement($data);
    }

    /**
     * Call read API until the last post was reached.
     *
     * @param array $query
     * @return \SimpleXMLElement[]
     */
    public function readAll(array $query)
    {
        if (!array_key_exists('start', $query)) {
            $query['start'] = 0;
        }

        if (!array_key_exists('num', $query)) {
            $query['num'] = 50;
        }

        $posts = array();
        $start = $query['start'];

        do {
            $query['start'] = $start;

            $xml   = $this->read($query);
            $currentPosts = $xml->xpath('posts//post');
            $posts = array_merge($posts, $currentPosts);

            $start = (int)$xml->posts->attributes()->start + $query['num'];
        } while(count($currentPosts) == $query['num'] && $start <= (int)$xml->posts['total']);

        return $posts;
    }

    protected function buildQueryString($query)
    {
        $values = array();

        foreach ($query as $key => $value) {
            $values[] = sprintf('%s=%s', $key, $value);
        }

        return '?' . implode('&', $values);
    }

    // extract these methods to abstract or other api client class

    public function callApi($url)
    {
        //TODO dispatch event: onCallApi -> $url
        //TODO allow file_get_contents
        return $this->curl($url);
    }

    public function curl($url)
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_POST, 0);
        curl_setopt($curl, CURLOPT_URL, $url);

        $data = curl_exec($curl);
        $info = curl_getinfo($curl);

        if ($info['http_code'] != 200) {
            $message = sprintf('Failed to call API. status code: %d', $info['http_code']);

            throw new \RuntimeException($message);
        }

        curl_close($curl);

        return $data;
    }
}
