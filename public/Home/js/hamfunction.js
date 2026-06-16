$(document).ready(function () {
  // $("input[name=coinNumber]").on("change paste keyup", function () {
  //   val = $(this).val();
  //   if ($.isNumeric(val * 100) && val >= 10) {
  //     $("#convertCoinNumber").html("<span class='text-success'>= " + addCommas(val * 10) + " KNB + " + addCommas(val * 10) + " KNB Khóa</span>");
  //   } else {
  //     // $("#convertCoinNumber").html("<span class='text-danger'>(Phải nhập bội số của 10)</span>");
  //   }
  // });

  $("#posts__tab.top3 li a").click(function (d) {
    d.preventDefault();
    var link = "tin-tuc.html";
    $("#posts__tab.top3 li a").removeClass("active");
    $(this).addClass("active");
    var str = this.rel;
    $("#posts__list.news").hide();
    $("#posts__list.news.item" + str).show();
    if (str == "tin-tuc") {
      link = "su-kien/danh-sach.1.html";
    } else if (str == "su-kien") {
      link = "huong-dan.html";
    } else if (str == "huong-dan") {
      link = "cong-dong.html";
    } else if (str == "giai-dau") {
      link = "tinh-nang.html";
    } else if (str == 1) {
      link = "tin-tuc/danh-sach.1.html";
    }

    $("a#posts__view-all").attr("href", link);
    return false;
  });
  $("#posts__tab.bottom li a").click(function (d) {
    d.preventDefault();
    $("#posts__tab.bottom li a").removeClass("active");
    $(this).addClass("active");
    var str = this.rel;
    $(".post-mh").hide();
    $(".post-mh.item" + str).show();
    return false;
  });

  $("#posts__tab.bottom li a").bind("mouseover", function (d) {
    d.preventDefault();
    $("#posts__tab.bottom li a").removeClass("active");
    $(this).addClass("active");
    var str = this.rel;
    $(".post-mh").hide();
    $(".post-mh.item" + str).show();
    return false;
  });

  $(".chat-box .title_bar").click(function (d) {
    d.preventDefault();
    if ($(this).parent().hasClass("active")) {
      $(this).parent().removeClass("active");
    } else {
      $(this).parent().addClass("active");
    }
  });

  //$("a#thiennu_dowload").attr("href","#");
  $("a#thiennu_dowload").click(function (d) {
    //d.preventDefault();
    //alert('Tải Thiện Nữ Mobile trên Appstore & Google Play vào ngày 16/07/2017');
  });

  $(".post-mh.mon_phai .nam_nu a").click(function (d) {
    d.preventDefault();
    var str = this.rel;
    var data_id = $(this).attr("data-id");

    $(".post-mh.mon_phai .nam_nu.id_" + data_id + " a").removeClass("active");
    $(this).addClass("active");
    $(".img_nam.id_" + data_id).hide();
    $(".img_nam.id_" + data_id + "." + str).show();
  });

  $(".menu_sub ul li span").click(function (d) {
    d.preventDefault();
    $(this).parent().find("ul").toggle();
    if ($(this).hasClass("holder")) {
      $(this).removeClass("holder").addClass("holder-open");
    } else {
      if ($(this).hasClass("holder-open")) {
        $(this).removeClass("holder-open").addClass("holder");
      }
    }
  });

  $(".game_tv_video .right ul li a").click(function (d) {
    d.preventDefault();
    var link_img = $(this).attr("rel");
    var link_video = $(this).attr("href");
    $(".game_tv_video .right ul li").removeClass("active");
    $(this).parent().addClass("active");
    $(".game_tv_video .left a").attr("href", link_video);
    $(".game_tv_video .left a img").attr("src", link_img);
  });

  var btnToggle = $(".fixed-box .toggle");
  var fixedbox = $(".fixed-box");
  btnToggle.unbind("click").bind("click", function (e) {
    e.preventDefault();

    if (btnToggle.hasClass("open")) {
      fixedbox.animate(
        {
          right: 0,
        },
        500,
        function () {
          btnToggle.removeClass("open").addClass("close");
        }
      );
    } else {
      fixedbox.animate(
        {
          right: -200,
        },
        500,
        function () {
          btnToggle.removeClass("close").addClass("open");
        }
      );
    }
  });
  var playing = false;
  // document.getElementById('audioBackground').play();
  // $('#boxAudio a.sound').unbind('click').bind('click', function(e) {
  // 	e.preventDefault();
  //     $(this).toggleClass("pause");
  //     if (playing == false) {
  //         document.getElementById('audioBackground').play();
  //         playing = true;
  //     } else {
  //         document.getElementById('audioBackground').pause();
  //         playing = false;
  //     }
  // });

  // $("#myAudio").attr('src','video/'+randomAudio+'.mp3');
  // var audio = document.getElementById('myAudio');
  // audio.load(); //call this to just preload the audio without playing
  // var promise = audio.play();
  // if (promise !== undefined) {
  //     console.log(promise);
  //     promise.then(_ => {
  //         audio.play();
  //         $(".icon-mute").addClass('d-none').removeClass('d-block')
  //         $(".music-wrap").addClass('d-block').removeClass('d-none')
  //     }).catch(error => {
  //         $(".music-wrap").addClass('d-none').removeClass('d-block')
  //         $(".icon-mute").addClass('d-block').removeClass('d-none')
  //     });
  // }

  // window.onload = function() {
  //     $('.loading').addClass("d-none");
  // }
});

function addCommas(nStr) {
  nStr += "";
  x = nStr.split(".");
  x1 = x[0];
  x2 = x.length > 1 ? "." + x[1] : "";
  var rgx = /(\d+)(\d{3})/;
  while (rgx.test(x1)) {
    x1 = x1.replace(rgx, "$1" + "," + "$2");
  }
  return x1 + x2;
}

function loadDing(str) {
  document.getElementById(str).innerHTML = "<img src='" + base_url + "/img/load/ajax-loader_1.gif' />";
}

function freload() {
  location.reload(true);
}

function controlMusic(objControl) {
  var myAudio = document.getElementById("audioBackground");
  if (!objControl.hasClass("pause")) {
    myAudio.pause();
    objControl.addClass("pause");
  } else {
    myAudio.play();
    objControl.removeClass("pause");
  }
}

function getCharacterList() {
  // $("select[name=serverId]").val();
  // console.log(base_url + "tai-khoan/api/server/getCharacterList/" + platformName + "/" + $('select[name=serverId]').val())
  if ($("select[name=serverId]").val() != "") {
    $.ajax({
      url: base_url + "tai-khoan/api/server/getCharacterList/" + platformName + "/" + $("select[name=serverId]").val(),
      type: "GET",
      cache: false,
      timeout: 20000,
      success: function (responseData) {
        if (responseData != "") {
          $("select[name=characterId]").find("option").remove().end();

          characterList = JSON.parse(responseData);

          $.each(characterList, function (key, value) {
            var o = new Option(value["role_name"], value["role_id"]);
            $(o).html(value["role_name"]);
            $("select[name=characterId]").append(o);
          });
        }
      },
    }).fail(function (jqXHR, textStatus, errorThrown) {
      $("select[name=characterId]").find("option").remove().end();
      console.log(textStatus);
      alert("Không lấy được danh sách nhân vật.");
    });
  } else {
    $("select[name=characterId]").find("option").remove().end();
  }
}

function getCurrentCoin() {
  let svId = $("select[name=serverId]").val();
  if (svId != "") {
    $.ajax({
      url: base_url + "tai-khoan/api/server/getCurrentCoin/" + platformName + "/" + svId,
      type: "GET",
      cache: false,
      timeout: 20000,
      success: function (responseData) {
        if (responseData != "") {
          res = JSON.parse(responseData);
          $("#nCoin").html(res["coin"]);
        }
      },
    }).fail(function (jqXHR, textStatus, errorThrown) {
      $("#nCoin").html(0);
      alert("Không lấy được số xu hiện tại.");
    });
  } else {
      $("#nCoin").html(0);
  }
}

$(document).ready(function () {
  $("select[name=serverId]").change(function () {
    getCharacterList();
    getCurrentCoin();
  });
  // console.log(base_url + "tai-khoan/api/server/getSvList/" + platformName)
  $.ajax({
    url: base_url + "tai-khoan/api/server/getSvList/" + platformName,
    type: "GET",
    cache: false,
    timeout: 10000,
    success: function (responseData) {
      if (responseData != "") {
        var o = new Option("", "");
        $(o).html("");
        $("select[name=serverId]").append(o);

        svList = JSON.parse(responseData);

        $.each(svList, function (key, value) {
          var o = new Option(value["name"], key);
          $(o).html(value["name"]);
          $("select[name=serverId]").append(o);
          //   getCharacterList();
        });
      }
    },
  }).fail(function (jqXHR, textStatus, errorThrown) {
    // alert('Không lấy được danh sách server.' + base_url + "tai-khoan/api/server/getSvList/" + platformName);
  });
});
