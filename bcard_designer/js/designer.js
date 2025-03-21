(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.bcardDesigner = {
    attach: function (context, settings) {
      var canvas = new fabric.Canvas('c');
      
      // Front/Back face functions
      window._Front_Face = function() {
        // Implement front face logic
      };
      
      window._Back_Face = function() {
        // Implement back face logic
      };
      
      // Shape creation functions
      window._Rect_Create = function() {
        var rect = new fabric.Rect({
          left: 100,
          top: 100,
          fill: 'red',
          width: 20,
          height: 20
        });
        canvas.add(rect);
      };
      
      window._Circle_Create = function() {
        var circle = new fabric.Circle({
          radius: 20,
          fill: 'green',
          left: 100,
          top: 100
        });
        canvas.add(circle);
      };
      
      window._Triangle_Create = function() {
        var triangle = new fabric.Triangle({
          width: 20,
          height: 30,
          fill: 'blue',
          left: 100,
          top: 100
        });
        canvas.add(triangle);
      };
      
      // Text creation function
      window._Text_Create = function() {
        var text = new fabric.IText('Your text here', {
          left: 100,
          top: 100,
        });
        canvas.add(text);
      };
      
      // Color change function
      window._Color_Change = function() {
        var activeObject = canvas.getActiveObject();
        if (activeObject) {
          activeObject.set('fill', $('#colordesigner').val());
          canvas.renderAll();
        }
      };
      
      // Delete item function
      $('#Delete_item').click(function() {
        var activeObject = canvas.getActiveObject();
        if (activeObject) {
          canvas.remove(activeObject);
        }
      });
      
      // Layer management functions
      // Layer management functions
      window._Bring_Forward = function() {
        var activeObject = canvas.getActiveObject();
        if (activeObject) {
          canvas.bringToFront(activeObject);
        }
      };
      
      window._Backwards = function() {
        var activeObject = canvas.getActiveObject();
        if (activeObject) {
          canvas.sendToBack(activeObject);
        }
      };
      
      window._Bring_Forward_One_Layer = function() {
        var activeObject = canvas.getActiveObject();
        if (activeObject) {
          canvas.bringForward(activeObject);
        }
      };
      
      window._Backwards_One_Layer = function() {
        var activeObject = canvas.getActiveObject();
        if (activeObject) {
          canvas.sendBackwards(activeObject);
        }
      };
      
      // Font change functions
      window._Change_Font = function() {
        var activeObject = canvas.getActiveObject();
        if (activeObject && activeObject.type === 'i-text') {
          activeObject.set('fontFamily', $('#fontFamily').val());
          canvas.renderAll();
        }
      };
      
      window._Change_FontSize = function() {
        var activeObject = canvas.getActiveObject();
        if (activeObject && activeObject.type === 'i-text') {
          activeObject.set('fontSize', parseInt($('#fontSize').val()));
          canvas.renderAll();
        }
      };
      
      // Text style functions
      window._Text_Bold = function() {
        var activeObject = canvas.getActiveObject();
        if (activeObject && activeObject.type === 'i-text') {
          activeObject.set('fontWeight', activeObject.fontWeight === 'bold' ? 'normal' : 'bold');
          canvas.renderAll();
        }
      };
      
      window._Text_Italic = function() {
        var activeObject = canvas.getActiveObject();
        if (activeObject && activeObject.type === 'i-text') {
          activeObject.set('fontStyle', activeObject.fontStyle === 'italic' ? 'normal' : 'italic');
          canvas.renderAll();
        }
      };
      
      window._Text_Underline = function() {
        var activeObject = canvas.getActiveObject();
        if (activeObject && activeObject.type === 'i-text') {
          activeObject.set('underline', !activeObject.underline);
          canvas.renderAll();
        }
      };
      
      // Background functions
      window._Change_Background = function() {
        var bgUrl = $('#backgroundSelect').val();
        if (bgUrl) {
          fabric.Image.fromURL(bgUrl, function(img) {
            canvas.setBackgroundImage(img, canvas.renderAll.bind(canvas), {
              scaleX: canvas.width / img.width,
              scaleY: canvas.height / img.height
            });
          });
        } else {
          canvas.backgroundImage = null;
          canvas.renderAll();
        }
      };
      
      window._Delete_Background_Image = function() {
        canvas.backgroundImage = null;
        canvas.renderAll();
        $('#backgroundSelect').val('');
      };
      
      // Image upload function
      $('#ImageData').change(function(e) {
        var file = e.target.files[0];
        var reader = new FileReader();
        reader.onload = function(f) {
          var data = f.target.result;
          fabric.Image.fromURL(data, function(img) {
            var oImg = img.set({left: 0, top: 0, angle: 0}).scale(0.9);
            canvas.add(oImg).renderAll();
            var a = canvas.setActiveObject(oImg);
            canvas.renderAll();
          });
        };
        reader.readAsDataURL(file);
      });
      
      // Save function
      $('#file_save').click(function() {
        var json = JSON.stringify(canvas.toJSON());
        // Here you would typically send this JSON to the server
        console.log(json);
        alert('Design saved!');
      });
    }
  };
})(jQuery, Drupal, drupalSettings);