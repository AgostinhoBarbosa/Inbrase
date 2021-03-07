<?php
    
    use Adianti\Widget\Form\TButton;
    
    class HWebcam {
        private $width;
        private $height;
        private $input;
        private $btnGravar;

        function __construct( $width, $height ) {

            $this->width  = $width;
            $this->height = $height;

        }

        public function setInput( $input ) {
            $this->input = $input;
        }

        public function setGravar( $button )
        {
            $this->btnGravar = $button;
        }

        public function show() {

            TScript::create(
                "

			  var player = document.getElementById('player'); 
			  var snapshotCanvas = document.getElementById('snapshot');
			  var captureButton = document.getElementById('capture');
			
			  var handleSuccess = function(stream) {
			    player.srcObject = stream;
			  };

			  $('#capture').on('click', function() {
			    var context = snapshot.getContext('2d');
	
			    context.drawImage(player, 0, 0, snapshotCanvas.width, snapshotCanvas.height);
			    showValue(snapshotCanvas.toDataURL());
			  });

			  navigator.mediaDevices.getUserMedia({video: true}).then(handleSuccess);

			  function showValue(val){
			   	$.post('hlib.php',{data:val})
			   	.then(res => $('#{$this->input->id}').val(res))
			   	.catch(err => console.log(err));		
			    $('#{$this->btnGravar->id}').css({display:'block'});
			    $('#{$this->btnGravar->id}').css({display:'block'});
  
			   };   
				"
            );

            $video               = new TElement( 'video' );
            $video->{'id'}       = 'player';
            $video->{'controls'} = 'true';
            $video->{'autoplay'} = 'true';
            $video->{'width'}    = 430;
            $video->{'height'}   = $this->height;

            $button = new TButton( 'gravar' );
            $button->setLabel( 'Capturar Imagem' );
            $button->style = 'margin-top: -26px;margin-left:20px;margin-right:20px;';
            $canvas = new TElement( 'canvas' );

            $canvas->{'width'}  = 430;
            $canvas->{'height'} = $this->height;

            $button->{'id'} = 'capture';
            $canvas->{'id'} = 'snapshot';

            THBox::pack( $video, $button, $canvas, $this->btnGravar )->show();

        }

    }

