<?php

/**
 * Fedora Commons v4 REST API interface
 * Version 1.0
 * Stuart J. Chalk
 * Created: 2017-05-31
 *
 * https://wiki.duraspace.org/display/FEDORA471/RESTful+HTTP+API
 *
 */
class Restapi extends FedoraAppModel
{
    public $useTable = false;

    public $headers=[];
    public $code="";
    public $phrase="";
    public $debug=false;

    // Getters

    public function getHeaders()
    {
        return $this->headers;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getPhrase()
    {
        return $this->phrase;
    }

    // Setters

    public function setDebug($value)
    {
        if($value=true|$value=false) {
            $this->debug=$value;
        }
    }

    // Private functions

    /**
     * Request handler
     * @param $path - path to object
     * @param $headers - what to send with request
     * @param $method - HTTP method
     * @param $body - file contents
     * @return string
     */
    private function request($path,$headers=[],$method='GET',$body='')
    {
        $socket= new HttpSocket();
        if($method=='GET') {
            if(!isset($headers['Accept'])) { $headers['Accept']='application/ld+json'; }
        }
        $request=[
            'method' => $method,
            'uri' => [
                'scheme' => Configure::read('scheme'),
                'host' => Configure::read('host'),
                'port' => Configure::read('port'),
                'path' => Configure::read('path').$path
            ],
            'header' => $headers,
            'body' => $body
        ];

        // Execute
        if($this->debug) { debug($request); }
        $response=$socket->request($request);
        if($this->debug) { debug($response);exit; }

        // Save response info
        $this->headers=$response->headers;
        $this->code=$response->code;
        $this->phrase=$response->reasonPhrase;

        // Return body
        $body=$response->body;
        $resp=json_decode($body,true);

        return $resp;
    }

    // Actions

    public function get($resource,$meta=true)
    {
        // Get a resource
        if($meta) { $resource.='/fcr:metadata'; }
        $meta=$this->request($resource,['Prefer'=>'return=representation']);

        return $meta;
    }

    public function add($resource,$type='',$file='')
    {
        // Add a resource: assumes that its a container when no content-type provided

        // POST: Create a new child node (random path)            curl -X POST "http://localhost:8080/rest/"
        // PUT: Create a new child node (specfic path)            curl -i -X PUT "http://localhost:8080/rest/node/to/create"
        $headers=[];$method='PUT';
        $filename=str_replace(Configure::read('dir.uploads').DS,'',$file);
        if($type!='') {
            $headers['Content-Type']=$type;
            $headers['Content-Transfer-Encoding']='binary';
            $headers['Content-Disposition']='attachment; filename="'.$filename.'"';
        }
        $body='';
        if($file!='') {
            $fp=fopen($file,'r');
            $body=fread($fp,filesize($file));
            fclose($fp);
        }

        $meta=$this->request($resource,$headers,$method,$body);

        // Check response and then errors

        return $meta;
    }

    public function update($resource)
    {
        // Update a resource

    }

    public function remove($resource)
    {
        // Add a resource

    }

    public function transaction()
    {
        // Methods: GET and POST

        // GET: -> Current status of transaction   curl -i "http://localhost:8080/rest/tx:<ID>"

        // POST:
        //   Create a new transaction              curl -i -X POST "http://localhost:8080/rest/fcr:tx"
        //   Keep an existing transaction alive    curl -i -X POST "http://localhost:8080/rest/tx:<ID>/fcr:tx"
        //   Save and commit an open transaction   curl -i -X POST "http://localhost:8080/rest/tx:<ID>/fcr:tx/fcr:commit"
        //   Rollback a transaction                curl -i -X POST "http://localhost:8080/rest/tx:<ID>/fcr:tx/fcr:rollback"

    }

    public function fixity()
    {
        // Method: GET     curl -H "Accept: text/turtle" "http://localhost:8080/rest/path/to/some/resource/fcr:fixity"


    }

    public function backup()
    {
        // Method: POST    curl -X POST "http://localhost:8080/rest/fcr:backup"

    }

    public function restore()
    {
        // Method: POST    curl -X POST --data-binary "/tmp/fcrepo4-data/path/to/backup/directory" "http://localhost:8080/rest/fcr:restore"

    }

    public function versions($resource,$type='list',$name='')
    {
        // A version is a snapshot of a resource at a particular time.  It is not automatic.  Use a transaction to make it automatic.
        // Methods: GET, POST, PATCH and DELETE

        // GET: Get a list of versions          curl -H "Accept: text/turtle" http://localhost:8080/rest/path/to/resource/fcr:versions
        // GET: Get a version                   curl http://localhost:8080/rest/path/to/resource/fcr:versions/<version-label>
        // POST: Create a new version           curl -X POST -H "Slug: newVersionName" http://localhost:8080/rest/path/to/resource/fcr:versions
        // PATCH: Revert to a previous version  curl -X PATCH http://localhost:8080/rest/path/to/resource/fcr:versions/existingVersionName
        // DELETE: Remove a version             curl -X DELETE http://localhost:8080/rest/path/to/resource/fcr:versions/versionName
        // (to delete the latest version, change to the previous one and then delete the latest)

        $headers=[];$method='GET';
        if($type=='list')       { $resource.='/fcr:versions'; }
        if($type=='version')    { $resource.='/fcr:versions/'.$name.'/fcr:metadata'; }
        if($type=='add')        { $resource.='/fcr:versions';$method='POST';$headers['Slug']=$name; } // 201 OK, 404 No resource, 409 Conflict
        if($type=='revert')     { $resource.='/fcr:versions/'.$name;$method='PATCH'; } // 204 No content, 404 Not found
        if($type=='delete')     { $resource.='/fcr:versions/'.$name;$method='DELETE'; } // 204 No content, 400 Bad request, 404 Not found

        $meta=$this->request($resource,$headers,$method);

        // Check response and then errors

        return $meta;
    }

    /**
     * Error handler
     */
    public function error()
    {

    }

}