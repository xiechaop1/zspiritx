var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var Lock = function () {
  function Lock() {
    _classCallCheck(this, Lock);
     var pin_code=$("input[name='pin_code']").val();
     // alert(pin_code);

    this.pin = pin_code;
    this.setupDom();
    this.setupFlickity();
    this.setupAudio();
    this.onResize();
    this.listen();
  }

  _createClass(Lock, [{
    key: 'listen',
    value: function listen() {
      var _this = this;

      window.addEventListener('resize', function () {
        return _this.onResize();
      });
    }
  }, {
    key: 'onResize',
    value: function onResize() {
      if (window.innerWidth % 2 === 0) {
        this.dom.lock.style.marginLeft = '0px';
      } else {
        this.dom.lock.style.marginLeft = '1px';
      }
    }
  }, {
    key: 'onChange',
    value: function onChange() {
      this.sounds.select.play();
      this.code = this.getCode();
      this.dom.code.textContent = this.code;
      if (this.code === this.pin) {
        this.verified = true;
        this.dom.lock.classList.add('verified');
        this.dom.status.textContent = 'UNLOCKED';
        this.sounds.success.play();

        var user_id=$("input[name='user_id']").val();
        var session_id=$("input[name='session_id']").val();
        var session_stage_id=$("input[name='session_stage_id']").val();
        var qa_id=$("input[name='qa_id']").val();
        var story_id=$("input[name='story_id']").val();
        $.ajax({
          type: "GET", //用POST方式传输
          dataType: "json", //数据格式:JSON
          async: false,
          url: '/qa/add_user_answer',
          data:{
            user_id:user_id,
            qa_id:qa_id,
            answer:this.pin,
            story_id:story_id,
            session_id:session_id,
            session_stage_id:session_stage_id
          },
          error: function (XMLHttpRequest, textStatus, errorThrown) {
            console.log("ajax请求失败:"+XMLHttpRequest,textStatus,errorThrown);
            // $.alert("网络异常，请检查网络情况");
          },
          success: function (data, status){
            var dataContent=data;
            var dataCon=$.toJSON(dataContent);
            var obj = eval( "(" + dataCon + ")" );//转换后的JSON对象
            //console.log("ajax请求成功:"+data.toString())
            //新消息获取成功
            if(obj["code"]==200){
              $("#answer-box").removeClass('hide');
              $("#answer-right-box").removeClass('hide');
              // $("#h5-right").modal('show');
              setTimeout(function () {
                // Unity.call('WebViewOff&TrueAnswer');
                var params = {
                    'WebViewOff':1,
                    'AnswerType':1
                }
                var data=$.toJSON(params);
                Unity.call(data);
                // $("#answer-right-box").addClass('hide');
              }, 4000);
            }
            //新消息获取失败
            else{
              // $.alert(obj.msg)
              console.log(obj);
            }

          }
        });
        // Unity.call('密码正确');

      } else {
        this.dom.lock.classList.remove('verified');
        this.dom.status.textContent = 'LOCKED';
        if (this.verified) {
          this.sounds.fail.play();
        }
        this.verified = false;
      }
    }
  }, {
    key: 'getCode',
    value: function getCode() {
      var code = '';
      for (var i = 0, len = this.dom.rows.length; i < len; i++) {
        var cell = this.dom.rows[i].querySelector('.is-selected .text');
        var num = cell.textContent;
        code += num;
      }
      return code;
    }
  }, {
    key: 'setupDom',
    value: function setupDom() {
      this.dom = {};
      this.dom.lock = document.querySelector('.lock');
      this.dom.rows = document.querySelectorAll('.row');
      this.dom.code = document.querySelector('.code');
      this.dom.status = document.querySelector('.status');
    }
  }, {
    key: 'setupAudio',
    value: function setupAudio() {
      this.sounds = {};

      this.sounds.select = new Howl({
        src: ['https://jackrugile.com/sounds/misc/lock-button-1.mp3', 'https://jackrugile.com/sounds/misc/lock-button-1.ogg'],
        volume: 0.5,
        rate: 1.4
      });

      this.sounds.prev = new Howl({
        src: ['https://jackrugile.com/sounds/misc/lock-button-4.mp3', 'https://jackrugile.com/sounds/misc/lock-button-4.ogg'],
        volume: 0.5,
        rate: 1
      });

      this.sounds.next = new Howl({
        src: ['https://jackrugile.com/sounds/misc/lock-button-4.mp3', 'https://jackrugile.com/sounds/misc/lock-button-4.ogg'],
        volume: 0.5,
        rate: 1.2
      });

      this.sounds.hover = new Howl({
        src: ['https://jackrugile.com/sounds/misc/lock-button-1.mp3', 'https://jackrugile.com/sounds/misc/lock-button-1.ogg'],
        volume: 0.2,
        rate: 3
      });

      this.sounds.success = new Howl({
        src: ['https://jackrugile.com/sounds/misc/lock-online-1.mp3', 'https://jackrugile.com/sounds/misc/lock-online-1.ogg'],
        volume: 0.5,
        rate: 1
      });

      this.sounds.fail = new Howl({
        src: ['https://jackrugile.com/sounds/misc/lock-fail-1.mp3', 'https://jackrugile.com/sounds/misc/lock-fail-1.ogg'],
        volume: 0.6,
        rate: 1
      });
    }
  }, {
    key: 'setupFlickity',
    value: function setupFlickity() {
      var _this2 = this;

      var _loop = function _loop(i, len) {
        var row = _this2.dom.rows[i];
        var flkty = new Flickity(row, {
          selectedAttraction: 0.25,
          friction: 0.9,
          cellAlign: 'center',
          pageDots: false,
          wrapAround: true
        });
        flkty.lastIndex = 0;

        flkty.on('select', function () {
          if (flkty.selectedIndex !== flkty.lastIndex) {
            _this2.onChange();
          }
          flkty.lastIndex = flkty.selectedIndex;
        });

        row.addEventListener('mouseenter', function () {
          _this2.sounds.hover.play();
        });
      };

      for (var i = 0, len = this.dom.rows.length; i < len; i++) {
        _loop(i, len);
      }

      this.dom.prevNextBtns = this.dom.lock.querySelectorAll('.flickity-prev-next-button');

      var _loop2 = function _loop2(i, len) {
        var btn = _this2.dom.prevNextBtns[i];
        btn.addEventListener('click', function () {
          if (btn.classList.contains('previous')) {
            _this2.sounds.prev.play();
          } else {
            _this2.sounds.next.play();
          }
        });
      };

      for (var i = 0, len = this.dom.prevNextBtns.length; i < len; i++) {
        _loop2(i, len);
      }
    }
  }]);

  return Lock;
}();

var lock = new Lock();

$("#return_btn").click(function (){
  var params = {
    'WebViewOff':1,
    'AnswerType':2
  }
  var data=$.toJSON(params);
  Unity.call(data);
});