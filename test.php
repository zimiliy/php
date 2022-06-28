

<?php echo $this->Form->create('Attachment', array('enctype' => 'multipart/form-data')); ?>
<?php echo $this->Form->file('attachment'); ?>
<?php echo $this->Form->submit('アップロード'); ?>
<?php echo $this->Form->end(); ?>

<?php
public function hoge() {
    if (!empty($this->request->data)) {
        $data = array(
            'Attachment' => array(
                'filename' => $this->request->data['Attachment']['attachment']['name'],
                'type' => $this->request->data['Attachment']['attachment']['type'],
                'contents' => file_get_contents($this->request->data['Attachment']['attachment']['tmp_name']),
            )
        );
        if ($this->Attachment->save($data)) {
            $this->Session->setFlash('アップロードしました');
        } else {
            $this->Session->setFlash('アップロードできませんでした');
        }
    }
}
?>
<?php
function attachment($id)
{
    $attachment = $this->Attachment->findById($id);
 
    $this->layout = false;
    header('Content-type: ' . $attachment['Attachment']['type']);
    header('Content-Disposition: attachment; filename="' . $attachment['Attachment']['filename'] . '"');
    echo $attachment['Attachment']['contents'];
    exit;
}
?>