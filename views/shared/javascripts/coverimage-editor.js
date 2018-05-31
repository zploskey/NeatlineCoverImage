
Neatline.module('Editor.Exhibit.CoverImage', function(CoverImage) {

  CoverImage.Router = Neatline.Shared.Router.extend({

    routes: {
      coverimage: 'coverimage'
    },

    coverimage: function() {
      Neatline.execute('EDITOR:display', [
        'EDITOR:EXHIBIT',
        'EDITOR:COVERIMAGE'
      ]);
      Neatline.execute('EDITOR:EXHIBIT:activateTab', 'coverimage');
    }

  });

});

Neatline.module('Editor.Exhibit.CoverImage', function(CoverImage) {

  CoverImage.View = Neatline.Shared.View.extend({

    template:   '#coverimage-form-template',
    className:  'form-stacked coverimage',
    tagName:    'form',

    events: {
      'click a[name="save"]': 'save'
    },

    init: function() {
      this.model = new Neatline.Shared.Exhibit.Model();
      rivets.bind(this.$el, { exhibit: this.model });
    },

    save: function() {
      this.model.save(null, {
        success:  _.bind(this.onSaveSuccess, this),
        error:    _.bind(this.onSaveError, this)
      });
    },

    onSaveSuccess: function(obj) {
      console.log('saved successfully: ');
      console.log(obj);
      Neatline.execute('EDITOR:notifySuccess', 'Cover image saved.');
    },

    onSaveError: function(obj) {
      console.log('error while saving: ');
      console.trace();
      Neatline.execute('EDITOR:notifyError', 'Error saving the cover image.');
    }

  });

});

Neatline.module('Editor.Exhibit.CoverImage', function(CoverImage) {

  CoverImage.Controller = Neatline.Shared.Controller.extend({

    slug: 'EDITOR:COVERIMAGE',

    commands: ['display'],

    init: function() {
      this.router = new CoverImage.Router();
      this.view = new CoverImage.View();
    },

    display: function(container) {
      this.view.showIn(container);
    }

  });

});

Neatline.module('Editor.Exhibit.CoverImage', function(CoverImage) {

  CoverImage.addInitializer(function() {
    CoverImage.__controller = new CoverImage.Controller();
  });

});
