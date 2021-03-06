im.addElement = function(object,type) {
  var layer = new Kinetic.Layer();
  layer.elementType = layer;
  layer.add(object);
  object.setOffset([object.getWidth()/2,object.getHeight()/2]);
  layer.setOffset([-object.getWidth()/2,-object.getHeight()/2]);
  object.setX(im.center.x - Math.round(object.getWidth() / 2));
  object.setY(im.center.y - Math.round(object.getHeight() / 2));

  object.doppelganger = object.clone();
  if (type == 'image') object.doppelganger.setImage('');
  object.doppelganger.doppelganger = object;
  object.doppelganger.drawHitFunc = object.doppelganger.attrs.drawHitFunc = function(){return false};
  object.doppelganger.setFill('transparent');
  object.doppelganger.elementType = 'StokeClone';
  object.doppelganger.setStroke('blue');
  object.doppelganger._drawFunc = object.getDrawFunc();
  object.doppelganger.setListening(false);
  object.doppelganger.setDrawFunc(function(canvas){
    if (typeof this._drawFunc == "function") {
      this.setStrokeWidth(1/im.scale);
      this.setFill('transparent');
      if (type == 'image') { this.attrs.image = ''; }
      this._drawFunc(canvas);
    }
  });

  object.elementType = type;

  object.on('click',function(){
    im.fire('ClickedElement',this);
  });
  object._drawFunc = object.getDrawFunc();
  object.setDrawFunc(function(canvas) {
    for (var attr in this.attrs) {
      if (attr == 'drawFunc' ||
          attr == 'drawHitFunc' ||
          attr == 'strokeWidth' ||
          attr == 'fill'        ||
          attr == 'listening') continue;
      this.doppelganger.attrs[attr] = this.attrs[attr];
    }
    var offset = this.getOffset();
    this.doppelganger.setX(this.getX() + offset.x);
    this.doppelganger.setY(this.getY() + offset.y);
    this.doppelganger.setOffset(this.getOffset());
    this.doppelganger.setSize(this.getSize());
    im.foreground.draw();
    this._drawFunc(canvas);
  });

  object.on('mouseover',function(){
    this.hovered = true;
    //im.stage.setDraggable(false);
    im.setCursor('pointer');
  });
  object.on('mouseout',function(){
    //im.stage.setDraggable(true);
    if (this.hovered == true) {
      im.setCursor('');
      this.hovered = false;
    }
  });

  im.stage.add(layer);
  im.fire('newObject',{object:object,type:type});
  im.foreground.moveToTop();
  im.stage.draw();
};

im.on('backgroundBuilt',function(){
  if (im.activeElement !== undefined && im.activeElement.doppelganger !== undefined) {
    im.foreground.add(im.activeElement.doppelganger);
    im.activeElement.doppelganger.setPosition(im.activeElement.getPosition());
  }
});

im.setActiveElement = function(element) {
  if (element.defer) {
      return im.setActiveElement(element.defer);
  }
  if (im.activeElement == element) return;
  if (im.activeElement !== undefined && im.activeElement.doppelganger !== undefined) {
    im.activeElement.doppelganger.remove();
  }
  if (element === im.stage || element.nodeType == 'Stage') {
    im.trigger('ChangeActiveAction','ControlSet_Position');
    $('div.control-sets',im.controlContext).find('h4.active').removeClass('active');
  } else if (element.doppelganger !== undefined) {
    im.foreground.add(element.doppelganger);
    im.foreground.draw();
  }
  im.trigger('beforeChangeActiveElement',im.activeElement);
  im.alterCore('activeElement',element);
  im.trigger('changeActiveElement',element);
  im.stage.draw();
};
im.bind('ClickedElement',function(e) {
  im.setActiveElement(e.eventData);
});

im.bind('stageChanged',function(e){
  if (im.activeElement.getWidth() > im.stage.getScaledWidth() || im.activeElement .getHeight() > im.stage.getScaledHeight()) {
    im.setActiveElement(im.stage);
  }
});