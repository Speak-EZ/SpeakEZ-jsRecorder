<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>SpeakEZ in-browser recorder demo</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
  </head>
  <body>
    <?php 
      $type = $_GET['type'];
      $id = $_GET['id'];
      $instance_id = $_GET['instance_id'];
      $name = $_GET['name'];
      $message = $_GET['message'];
    ?>
    <div class="container">
      <h1>SpeakEZ in-browser recorder</h1>
      <h3>Type: <?php echo $type; ?></h3>
      <h3>Name: <?php echo $name . " (ID: " . $id . ", Instance ID: " . $instance_id . ")"; ?></h3>
      <h3>Message: <?php echo $message; ?>.wav</h3>
      <input type="hidden" id="r_type" name="r_type" value="<?php echo $type; ?>">
      <input type="hidden" id="r_id" name="r_id" value="<?php echo $id; ?>">
      <input type="hidden" id="r_instance_id" name="r_instance_id" value="<?php echo $instance_id; ?>">
      <input type="hidden" id="r_name" name="r_name" value="<?php echo $name; ?>">
      <input type="hidden" id="r_message" name="r_message" value="<?php echo $message; ?>">
      <hr>
      <div class="form-horizontal">
        <div class="form-group hide">
          <label class="col-sm-3 control-label">Audio input</label>
          <div class="col-sm-2">Test tone</div>
          <div class="col-sm-3">
            <input id="test-tone-level" type="range" min="0" max="100" value="0">
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-3 control-label">Audio Input</label>
          <div class="col-sm-4">
            <input id="microphone" type="checkbox"> Enable Microphone
          </div>
          <div class="col-sm-3">
            <input id="microphone-level" type="range" min="0" max="100" value="100" class="hidden">
          </div>
        </div>
        <div class="form-group hide">
          <label class="col-sm-3 control-label">Recording time limit</label>
          <div class="col-sm-2">
            <input id="time-limit" type="range" min="1" max="10" value="3">
          </div>
          <div id="time-limit-text" class="col-sm-7">3 minutes</div>
        </div>
        <div class="form-group hide">
          <label class="col-sm-3 control-label">Encoding</label>
          <div class="col-sm-3">
            <input type="radio" name="encoding" encoding="wav" checked> .wav &nbsp; 
            <input type="radio" name="encoding" encoding="ogg"> .ogg &nbsp; 
            <input type="radio" name="encoding" encoding="mp3"> .mp3
          </div>
          <label id="encoding-option-label" class="col-sm-2 control-label"></label>
          <div class="col-sm-2">
            <input id="encoding-option" type="range" min="0" max="11" value="6" class="hidden">
          </div>
          <div id="encoding-option-text" class="col-sm-2"></div>
        </div>
        <div class="form-group hide">
          <label class="col-sm-3 control-label">Encoding process</label>
          <div class="col-sm-9">
            <input type="radio" name="encoding-process" mode="background" checked> Encode on recording background
          </div>
        </div>
        <div class="form-group hide">
          <div class="col-sm-3"></div>
          <div class="col-sm-3">
            <input type="radio" name="encoding-process" mode="separate"> Encode after recording (safer)
          </div>
          <label id="report-interval-label" class="col-sm-2 control-label hidden">Reports every</label>
          <div class="col-sm-2">
            <input id="report-interval" type="range" min="1" max="5" value="1" class="hidden">
          </div>
          <div id="report-interval-text" class="col-sm-2 hidden">1 second</div>
        </div>
        <div class="form-group hide">
          <label class="col-sm-3 control-label">Recording buffer size</label>
          <div class="col-sm-2">
            <input id="buffer-size" type="range" min="0" max="6">
          </div>
          <div id="buffer-size-text" class="col-sm-7"></div>
        </div>
        <div class="form-group hide">
          <div class="col-sm-3"></div>
          <div class="col-sm-9 text-warning"><strong>Warning: </strong><span>setting size below browser default may fail recording.</span></div>
        </div>
        <div class="form-group">
          <div class="col-sm-3 control-label"><span id="recording" class="text-danger hidden"><strong>RECORDING</strong></span>&nbsp; <span id="time-display">00:00</span></div>
          <div class="col-sm-3">
            <button id="record" class="btn btn-danger">RECORD</button>
            <button id="cancel" class="btn btn-default hidden">CANCEL</button>
          </div>
          <div class="col-sm-6"><span id="date-time" class="text-info"></span></div>
        </div>
      </div>
      <hr>
      <h3>Playback</h3>
      <div id="recording-list"></div>
    </div>
    <div id="modal-loading" class="modal fade">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title"></h4>
          </div>
        </div>
      </div>
    </div>
    <div id="modal-progress" class="modal fade">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title"></h4>
          </div>
          <div class="modal-body">
            <div class="progress">
              <div style="width: 0%;" class="progress-bar"></div>
            </div>
            <div class="text-center">0%</div>
          </div>
          <div class="modal-footer">
            <button type="button" data-dismiss="modal" class="btn">Cancel</button>
          </div>
        </div>
      </div>
    </div>
    <div id="modal-error" class="modal fade">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" data-dismiss="modal" class="close">&times;</button>
            <h4 class="modal-title">Error</h4>
          </div>
          <div class="modal-body">
            <div class="alert alert-warning"></div>
          </div>
          <div class="modal-footer">
            <button type="button" data-dismiss="modal" class="btn btn-primary">Close</button>
          </div>
        </div>
      </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <script src="js/WebAudioRecorder.js"></script>
    <script src="js/RecorderDemo.js"></script>
  </body>
</html>
