<?php
/**
 * @package	Joomla
 * @subpackage	com_toes
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license	GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die;
$app = JFactory::getApplication();

JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
JHTML::_('behavior.modal');

$user = JFactory::getUser();
$isAdmin = TOESHelper::isAdmin();
$isEditor = TOESHelper::isEditor();
$params = JComponentHelper::getParams('com_toes');
?>

<script type="text/javascript">
	
    function removecat(cat_id,con_type)
    {
		new jBox('Confirm',{
	        content: "<?php echo JText::_('REMOVE_PROFILE_CAT_QUESTION'); ?>",
	        width: '400px',
	        cancelButton : NO_BUTTON,
	        confirmButton: YES_BUTTON,
	        confirm: function() {
	            jQuery.ajax({
	                url: 'index.php?option=com_toes&task=cats.removecat&cat_id='+cat_id+'&connection_type='+con_type+'&tmpl=component',
	                type: 'get',
	            }).done(function(responseText){
	                if(responseText == 1)
	                {
	                    location.reload(true);
	                }
	            });
	        }
		}).open();    
	}
	
	<?php if( ($isAdmin && $params->get('show_cat_search_for_admin')) || ($isEditor && $params->get('show_cat_search_for_cateditor')) ): ?>
		jQuery(document).ready(function(){
			var Itemid = '<?php echo $app->input->getInt('Itemid');?>';
			
			jQuery( "#srch_cat_name" ).autocomplete({
			  source: 'index.php?option=com_toes&task=cats.getCatByName&tmpl=component',
			  select: function( event, ui ) {
			  	//jQuery( "#user_id" ).val(ui.item.key);
			  	jQuery( "#srch_cat_name" ).val(ui.item.value);
			  	window.location = 'index.php?option=com_toes&view=cat&layout=edit&id='+ui.item.key+'&Itemid='+Itemid;
			  }
			});    
		
			jQuery( "#srch_reg_number" ).autocomplete({
			  source: 'index.php?option=com_toes&task=cats.getCatByRegnumber&tmpl=component',
			  select: function( event, ui ) {
			  	//jQuery( "#user_id" ).val(ui.item.key);
			  	jQuery( "#srch_reg_number" ).val(ui.item.value);
			  	window.location = 'index.php?option=com_toes&view=cat&layout=edit&id='+ui.item.key+'&Itemid='+Itemid;
			  }
			});
		});    
	<?php endif; ?>
	
</script>

<?php if( ($isAdmin && $params->get('show_cat_search_for_admin')) || ($isEditor && $params->get('show_cat_search_for_cateditor')) ): ?>
<div class="filter-block" style="float: left;">
	<div class="filter-field" >
		<input type="text" name="srch_cat_name" id="srch_cat_name" style="width: 200px;" placeholder="<?php echo JText::_('COM_TOES_TYPE_TO_SEARCH_BY_CAT_NAME'); ?>" />
	</div>
	<div class="filter-field" >
		<input type="text" name="srch_reg_number" id="srch_reg_number" style="width: 250px;" placeholder="<?php echo JText::_('COM_TOES_TYPE_TO_SEARCH_BY_CAT_REG_NUMBER'); ?>" />
	</div>
</div>	
<?php endif; ?>
<?php if($isAdmin): ?>
<div class="action-buttons" style="padding:15px 10px 0 0;" >
	<input class="add" type="button" name="add" value="<?php echo JText::_('COM_TOES_ADD_NEW_CAT'); ?>" onclick="window.location='<?php echo JRoute::_('index.php?option=com_toes&view=cat&layout=edit');?>'"/>
</div>
<?php endif; ?>

<div class="clr"></div>
<!-- Owner -->
<div class ="outerdiv">
	
    <div class ="fistouter">
        <div class="block-title" ><?php echo JText::_('COM_TOES_MY_CAT'); ?></div>
        <div class="clr"></div>
    </div>

    <div class ="seconouter">
        <div class="seconouter-row">
            <div class="seconouter-row1" ><?php echo JText::_('COM_TOES_REGISTRATION_NO'); ?> </div>
            <div class="seconouter-row2" ><?php echo JText::_('COM_TOES_NAME'); ?></div>
            <div class="seconouter-row3" ><?php echo JText::_('COM_TOES_BREED'); ?></div>
            <div class="seconouter-row4" ><?php echo JText::_('COM_TOES_GENDER'); ?></div>
            <div class="seconouter-row5" > <?php echo JText::_('COM_TOES_DOB'); ?> </div>
            <div class="seconouter-row6" >  </div>
            <div class="clr"></div>
        </div>
        <?php
        foreach ($this->datamycat as $data) {
            ?>
            <div class="seconouter-row-col">
                <div class="seconouter-row-col1" ><?php echo $data->cat_registration_number ? $data->cat_registration_number : 'PENDING'; ?></div>
                <div class="seconouter-row-col2" >
                    <?php
                    $catname = '';
                    if ($data->cat_prefix_abbreviation)
                        $catname .=$data->cat_prefix_abbreviation . ' ';
                    if ($data->cat_title_abbreviation)
                        $catname .=$data->cat_title_abbreviation . ' ';
                    if ($data->cat_suffix_abbreviation)
                        $catname .=$data->cat_suffix_abbreviation . ' ';

                    $catname .=$data->cat_name;

                    echo $catname;
                    ?>
                </div>
                <div class="seconouter-row-col3" ><?php echo $data->breed_name . ' (' . $data->breed_hair_length . ')'; ?></div>
                <div class="seconouter-row-col4" ><?php echo $data->gender_name; ?></div>
                <div class="seconouter-row-col5" ><?php echo $data->da; ?></div>
                <div class="seconouter-row-col6" >
                    <?php
                    if (TOESHelper::is_onlyuser($user->id, $data->cat_id) || (!TOESHelper::is_onlyuser($user->id, $data->cat_id) && TOESHelper::is_catowner($user->id, $data->cat_id))) {
                        ?>
                        <span class="hasTip" title="<?php echo JText::_('EDIT_CAT'); ?>">
                            <a href="<?php echo JRoute::_('index.php?option=com_toes&view=cat&layout=edit&id=' . $data->cat_id); ?>" class="edit-show" >
                                <i class="fa fa-edit large"></i>
                            </a>
                        </span>
                        <?php
                    }
                    ?>	

                    <span class="hasTip" title="<?php echo JText::_('REMOVE_CAT'); ?>">
                        <a href="javascript::void();" onclick="removecat('<?php echo $data->cat_id; ?>','Owner');" class="cancel-show" >
                            <i class="fa fa-trash large"></i>
                        </a>
                    </span>
                </div>
                <div class="clr"></div>
            </div>
            <?php
        }
        ?>

        <div class="seconouter-row-col">
            <div class="fistouter-row-left" style="padding: 8px 8px 25px;width:auto;"> 
                <input type="button" onclick="window.location='<?php echo JRoute::_('index.php?option=com_toes&view=cat&layout=edit&group=owner');?>'" name="add_a_cat" value="<?php echo JText::_('COM_TOES_ADD'); ?>">
                <div style="width:50%;float:left;"></div>
            </div>
            <div class="clr"></div>
        </div>
    </div>
</div>

<!-- Breeder -->
<div class ="outerdiv">
    <div class ="fistouter">
        <div class="block-title" ><?php echo JText::_('COM_TOES_OHTER_CAT_BREAD'); ?> </div>
        <div class="clr"></div>
    </div>

    <div class ="seconouter">
        <div class="seconouter-row" >
            <div class="seconouter-row1" ><?php echo JText::_('COM_TOES_REGISTRATION_NO'); ?> </div>
            <div class="seconouter-row2" ><?php echo JText::_('COM_TOES_NAME'); ?></div>
            <div class="seconouter-row3" ><?php echo JText::_('COM_TOES_BREED'); ?></div>
            <div class="seconouter-row4" ><?php echo JText::_('COM_TOES_GENDER'); ?></div>
            <div class="seconouter-row5" > <?php echo JText::_('COM_TOES_DOB'); ?> </div>
            <div class="seconouter-row6" >  </div>
            <div class="clr"></div>
        </div>

        <?php foreach ($this->databred as $data) { ?>
            <div class="seconouter-row-col">
                <div class="seconouter-row-col1" ><?php echo $data->cat_registration_number; ?></div>
                <div class="seconouter-row-col2" >
                    <?php
                    $catname = '';
                    if ($data->cat_prefix_abbreviation)
                        $catname .=$data->cat_prefix_abbreviation . ' ';
                    if ($data->cat_title_abbreviation)
                        $catname .=$data->cat_title_abbreviation . ' ';
                    if ($data->cat_suffix_abbreviation)
                        $catname .=$data->cat_suffix_abbreviation . ' ';

                    $catname .=$data->cat_name;

                    echo $catname;
                    ?>
                </div>
                <div class="seconouter-row-col3" ><?php echo $data->breed_name . ' (' . $data->breed_hair_length . ')'; ?></div>
                <div class="seconouter-row-col4" ><?php echo $data->gender_name; ?></div>
                <div class="seconouter-row-col5" ><?php echo $data->da; ?></div>
                <div class="seconouter-row-col6" >
                    <?php
                    if (TOESHelper::is_onlyuser($user->id, $data->cat_id) || (!TOESHelper::is_onlyuser($user->id, $data->cat_id) && TOESHelper::is_catowner($user->id, $data->cat_id))) {
                        ?>
                        <span class="hasTip" title="<?php echo JText::_('EDIT_CAT'); ?>">
                            <a href="<?php echo JRoute::_('index.php?option=com_toes&view=cat&layout=edit&id=' . $data->cat_id); ?>" class="edit-show" >
                                <i class="fa fa-edit large"></i>
                            </a>
                        </span>

                        <?php
                    }
                    ?>	

                    <span class="hasTip" title="<?php echo JText::_('REMOVE_CAT'); ?>">
                        <a href="javascript::void();" onclick="removecat('<?php echo $data->cat_id; ?>','Breeder');" class="cancel-show" >
                            <i class="fa fa-trash large"></i>
                        </a>
                    </span>
                </div>
                <div class="clr"></div>
            </div>
            <?php
        }
        ?>

        <div class="seconouter-row-col">
            <div class="fistouter-row-left" style="padding: 8px 8px 25px;width:auto;"> 
                <input type="button" onclick="window.location='<?php echo JRoute::_('index.php?option=com_toes&view=cat&layout=edit&group=breeder');?>'" name="add_a_cat" value="<?php echo JText::_('COM_TOES_ADD'); ?>"> 
                <div style="width:50%;float:left;"></div>
            </div>
            <div class="clr"></div>
        </div>


    </div>


</div>

<!-- Lessee-->
<div class ="outerdiv">
    <div class ="fistouter">
        <div class="block-title" ><?php echo JText::_('COM_TOES_LESSEE_CAT'); ?></div>
        <div class="clr"></div>
    </div>

    <div class ="seconouter">
        <div class="seconouter-row" >
            <div class="seconouter-row1" ><?php echo JText::_('COM_TOES_REGISTRATION_NO'); ?> </div>
            <div class="seconouter-row2" ><?php echo JText::_('COM_TOES_NAME'); ?></div>
            <div class="seconouter-row3" ><?php echo JText::_('COM_TOES_BREED'); ?></div>
            <div class="seconouter-row4" ><?php echo JText::_('COM_TOES_GENDER'); ?></div>
            <div class="seconouter-row5" > <?php echo JText::_('COM_TOES_DOB'); ?> </div>
            <div class="seconouter-row6" >  </div>
            <div class="clr"></div>
        </div>

        <?php
        foreach ($this->datalesseecat as $data) {
            ?>
            <div class="seconouter-row-col">
                <div class="seconouter-row-col1" ><?php echo $data->cat_registration_number; ?></div>
                <div class="seconouter-row-col2" >
                    <?php
                    $catname = '';
                    if ($data->cat_prefix_abbreviation)
                        $catname .=$data->cat_prefix_abbreviation . ' ';
                    if ($data->cat_title_abbreviation)
                        $catname .=$data->cat_title_abbreviation . ' ';
                    if ($data->cat_suffix_abbreviation)
                        $catname .=$data->cat_suffix_abbreviation . ' ';

                    $catname .=$data->cat_name;

                    echo $catname;
                    ?>
                </div>
                <div class="seconouter-row-col3" ><?php echo $data->breed_name . ' (' . $data->breed_hair_length . ')'; ?></div>
                <div class="seconouter-row-col4" ><?php echo $data->gender_name; ?></div>
                <div class="seconouter-row-col5" ><?php echo $data->da; ?></div>
                <div class="seconouter-row-col6" >
                    <?php
                    if (TOESHelper::is_onlyuser($user->id, $data->cat_id) || (!TOESHelper::is_onlyuser($user->id, $data->cat_id) && (TOESHelper::is_catowner($user->id, $data->cat_id) || TOESHelper::is_catlessee($user->id, $data->cat_id)))) {
                        ?>
                        <span class="hasTip" title="<?php echo JText::_('EDIT_CAT'); ?>">
                            <a href="<?php echo JRoute::_('index.php?option=com_toes&view=cat&layout=edit&id=' . $data->cat_id); ?>" class="edit-show" >
                                <i class="fa fa-edit large"></i>
                            </a>
                        </span>
                        <?php
                    }
                    ?>	

                    <span class="hasTip" title="<?php echo JText::_('REMOVE_CAT'); ?>">
                        <a href="javascript::void();" onclick="removecat('<?php echo $data->cat_id; ?>','Lessee');" class="cancel-show" >
                            <i class="fa fa-trash large"></i>
                        </a>
                    </span>
                </div>
                <div class="clr"></div>
            </div>
            <?php
        }
        ?>

        <div class="seconouter-row-col">
            <div class="fistouter-row-left" style="padding: 8px 8px 25px;width:auto;"> 
                <input type="button" onclick="window.location='<?php echo JRoute::_('index.php?option=com_toes&view=cat&layout=edit&group=lessee');?>'" name="add_a_cat" value="<?php echo JText::_('COM_TOES_ADD'); ?>"> 
                <div style="width:50%;float:left;"></div>
            </div>
            <div class="clr"></div>
        </div>

    </div>
</div>

<!-- agent-->

<div class ="outerdiv">
    <div class ="fistouter">
        <div class="block-title" ><?php echo JText::_('COM_TOES_AGENT_CAT'); ?></div>
        <div class="clr"></div>
    </div>

    <div class ="seconouter">
        <div class="seconouter-row" >
            <div class="seconouter-row1" ><?php echo JText::_('COM_TOES_REGISTRATION_NO'); ?> </div>
            <div class="seconouter-row2" ><?php echo JText::_('COM_TOES_NAME'); ?></div>
            <div class="seconouter-row3" ><?php echo JText::_('COM_TOES_BREED'); ?></div>
            <div class="seconouter-row4" ><?php echo JText::_('COM_TOES_GENDER'); ?></div>
            <div class="seconouter-row5" > <?php echo JText::_('COM_TOES_DOB'); ?> </div>
            <div class="seconouter-row6" >  </div>
            <div class="clr"></div>
        </div>

        <?php
        foreach ($this->dataagentcat as $data) {
            ?>
            <div class="seconouter-row-col">
                <div class="seconouter-row-col1" ><?php echo $data->cat_registration_number; ?></div>
                <div class="seconouter-row-col2" >
                    <?php
                    $catname = '';
                    if ($data->cat_prefix_abbreviation)
                        $catname .=$data->cat_prefix_abbreviation . ' ';
                    if ($data->cat_title_abbreviation)
                        $catname .=$data->cat_title_abbreviation . ' ';
                    if ($data->cat_suffix_abbreviation)
                        $catname .=$data->cat_suffix_abbreviation . ' ';

                    $catname .=$data->cat_name;

                    echo $catname;
                    ?>
                </div>
                <div class="seconouter-row-col3" ><?php echo $data->breed_name . ' (' . $data->breed_hair_length . ')'; ?></div>
                <div class="seconouter-row-col4" ><?php echo $data->gender_name; ?></div>
                <div class="seconouter-row-col5" ><?php echo $data->da; ?></div>
                <div class="seconouter-row-col6" >
                    <?php
                    if (TOESHelper::is_onlyuser($user->id, $data->cat_id) || (!TOESHelper::is_onlyuser($user->id, $data->cat_id) && TOESHelper::is_catowner($user->id, $data->cat_id))) {
                        ?>
                        <span class="hasTip" title="<?php echo JText::_('EDIT_CAT'); ?>">
                            <a href="<?php echo JRoute::_('index.php?option=com_toes&view=cat&layout=edit&id=' . $data->cat_id); ?>" class="edit-show" >
                                <i class="fa fa-edit large"></i>
                            </a>
                        </span>
                        <?php
                    }
                    ?>	

                    <span class="hasTip" title="<?php echo JText::_('REMOVE_CAT'); ?>">
                        <a href="javascript::void();" onclick="removecat('<?php echo $data->cat_id; ?>','Agent');" class="cancel-show" >
                            <i class="fa fa-trash large"></i>
                        </a>
                    </span>
                </div>
                <div class="clr"></div>
            </div>
            <?php
        }
        ?>

        <div class="seconouter-row-col">
            <div class="fistouter-row-left" style="padding: 8px 8px 25px;width:auto;"> 
                <input type="button" onclick="window.location='<?php echo JRoute::_('index.php?option=com_toes&view=cat&layout=edit&group=agent');?>'" name="add_a_cat" value="<?php echo JText::_('COM_TOES_ADD'); ?>"> 
                <div style="width:50%;float:left;"></div>
            </div>
            <div class="clr"></div>
        </div>
    </div>
</div>
