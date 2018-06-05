<?php

class NeatlineCoverImagePlugin extends Omeka_Plugin_AbstractPlugin
{

    const NAME  = 'Cover Image';
    const ID    = 'CoverImage';

    protected $_hooks = array(
        'install',
        'uninstall',
        'neatline_editor_static',
        'neatline_editor_templates',
        'neatline_exhibits_browse_sql',
    );

    protected $_filters = array(
        'neatline_exhibit_expansions',
        'neatline_exhibit_tabs',
        'neatline_exhibit_widgets',
    );

    /** Create the cover image exhibit expansion table. */
    public function hookInstall()
    {

        $this->_db->query(<<<SQL
        CREATE TABLE IF NOT EXISTS
        {$this->_db->prefix}neatline_cover_image_exhibit_expansions (
            id                      INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            parent_id               INT(10) UNSIGNED NULL,
            cover_image_file_id     INT(10) UNSIGNED NULL,
            PRIMARY KEY             (id)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SQL
        );
    }

    /** Remove the cover image exhibit expansion table. */
    public function hookUninstall()
    {
        $this->_db->query(<<<SQL
        DROP TABLE {$this->_db->prefix}neatline_cover_image_exhibit_expansions
SQL
        );
    }

    /** Add the cover image expansion table. */
    public function filterNeatlineExhibitExpansions($tables)
    {
        $tables[] = $this->_db->getTable('NeatlineCoverImageExhibitExpansion');
        return $tables;
    }

    /** Queue the javascript for the cover image form in the Exhibit editor. */
    public function hookNeatlineEditorStatic($args)
    {
        if ($args['exhibit']->hasWidget(self::ID)) {
            queue_js_file('coverimage-editor');
        }
    }

    /** Add the cover image form template. */
    public function hookNeatlineEditorTemplates($args)
    {
        if ($args['exhibit']->hasWidget(self::ID)) {
            echo get_view()->partial('coverimage/editor/form.php');
        }
    }

    /**
     * Fetch Cover Image files for Neatline Exhibits on Browse page.
     * They will be in the same order as the exhibits. Themes should
     * check that the file's id field is set, as the File may contain
     * null values if a Cover Image is not set for an exhibit.
     */
    public function hookNeatlineExhibitsBrowseSql($args)
    {
        $select = clone $args['select'];
        $select->reset('columns');
        $fileTable = $this->_db->getTable('File');
        $fileAlias = $fileTable->getTableAlias();
        $select->joinLeft(
            array($fileAlias => $fileTable->getTableName()),
            "cover_image_file_id = $fileAlias.id",
            array('*', 'neatline_exhibits.id AS neatline_exhibit_id')
        );
        get_view()->files = $fileTable->fetchObjects($select);
    }

    /** Add a tab in the Neatline Exhibit to configure the cover image. */
    public function filterNeatlineExhibitTabs($tabs, $args)
    {
        if ($args['exhibit']->hasWidget(self::ID)) {
            $tabs[self::NAME] = 'coverimage';
        }
        return $tabs;
    }

    /** Add Cover Image widget in Neatline Exhibit settings. */
    public function filterNeatlineExhibitWidgets($widgets)
    {
        return array_merge($widgets, array(self::NAME => self::ID));
    }

}
