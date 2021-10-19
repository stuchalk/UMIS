<?php
/**
 * Class FormatsController
 */
class FormatsController extends AppController
{

    public $uses = ['Encoding','Format','EncodingsFormat'];

    /**
     * beforeFilter function
     */
    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->Auth->allow();
    }

    /**
     * Encodings index
     * @return mixed
     */
    public function migrate()
    {
        $encs=$this->Encoding->find('all',['order'=>'Encoding.id','conditions'=>['NOT'=>['format'=>'ivoa']]]);
        $formats=$this->Format->find('list',['fields'=>['abbrev','id']]);
        foreach($encs as $enc) {
            if(empty($enc['Format'])) {
                $encarr=['format_id'=>$formats[$enc['Encoding']['format']],'encoding_id'=>$enc['Encoding']['id']];
                $this->EncodingsFormat->create();
                $done=$this->EncodingsFormat->save(['EncodingsFormat'=>$encarr]);
                if($done) {
                    echo $enc['Encoding']['string']." added (".$enc['Encoding']['format'].")";
                } else {
                    echo $enc['Encoding']['string']." error (".$enc['Encoding']['format'].")";exit;
                }
            } else {
                foreach($enc['Format'] as $format) {
                    if($enc['Encoding']['format']==$format['abbrev']) {
                        echo $enc['Encoding']['string']." already present (".$enc['Encoding']['format'].")";continue;
                    } else {
                        $encarr=['format_id'=>$formats[$enc['Encoding']['format']],'encoding_id'=>$enc['Encoding']['id']];
                        $this->EncodingsFormat->create();
                        $done=$this->EncodingsFormat->save(['EncodingsFormat'=>$encarr]);
                        if($done) {
                            echo $enc['Encoding']['string']." added (".$enc['Encoding']['format'].")";
                        } else {
                            echo $enc['Encoding']['string']." error (".$enc['Encoding']['format'].")";exit;
                        }
                    }
                }
            }
        }
        exit;
    }

}
