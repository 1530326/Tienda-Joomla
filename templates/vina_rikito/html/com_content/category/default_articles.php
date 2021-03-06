<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.framework');

// Create some shortcuts.
$params		= &$this->item->params;
$n			= count($this->items);
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
?>

<?php if (empty($this->items)) : ?>

	<?php if ($this->params->get('show_no_articles', 1)) : ?>
	<p><?php echo JText::_('COM_CONTENT_NO_ARTICLES'); ?></p>
	<?php endif; ?>

<?php else : ?>

<form action="<?php echo htmlspecialchars(JUri::getInstance()->toString()); ?>" method="post" name="adminForm" id="adminForm" class="form-inline">
	<?php if ($this->params->get('show_headings') || $this->params->get('filter_field') != 'hide' || $this->params->get('show_pagination_limit')) :?>
	<div class="filters btn-toolbar">
		<?php if ($this->params->get('filter_field') != 'hide') :?>
			<div class="btn-group">
				<label class="filter-search-lbl element-invisible" for="filter-search"><span class="label label-warning"><?php echo JText::_('JUNPUBLISHED'); ?></span><?php echo JText::_('COM_CONTENT_'.$this->params->get('filter_field').'_FILTER_LABEL').'&#160;'; ?></label>
				<input type="text" name="filter-search" id="filter-search" value="<?php echo $this->escape($this->state->get('list.filter')); ?>" class="inputbox" onchange="document.adminForm.submit();" title="<?php echo JText::_('COM_CONTENT_FILTER_SEARCH_DESC'); ?>" placeholder="<?php echo JText::_('COM_CONTENT_FILTER_SEARCH_DESC'); ?>" />
			</div>
		<?php endif; ?>
		<?php if ($this->params->get('show_pagination_limit')) : ?>
			<div class="btn-group pull-right">
				<label class="element-invisible">
					<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>
				</label>
				<?php echo $this->pagination->getLimitBox(); ?>
			</div>
		<?php endif; ?>

		<input type="hidden" name="filter_order" value="" />
		<input type="hidden" name="filter_order_Dir" value="" />
		<input type="hidden" name="limitstart" value="" />
		<div class="clearfix"></div>
	</div>
	<?php endif; ?>

	<ul class="category list-striped list-condensed">

		<?php foreach ($this->items as $i => $article) : ?>
			<?php if ($this->items[$i]->state == 0) : ?>
				<li class="system-unpublished cat-list-row<?php echo $i % 2; ?>">
			<?php else: ?>
				<li class="cat-list-row<?php echo $i % 2; ?>" >
			<?php endif; ?>
				<?php if (in_array($article->access, $this->user->getAuthorisedViewLevels())) : ?>
					<?php if ($this->params->get('list_show_hits', 1)) : ?>
					<span class="list-hits badge badge-info pull-right">

						<?php if (JVERSION<3) { ?>
							<?php echo $article->hits ?>
						<?php } else { ?>
							<?php echo JText::sprintf('JGLOBAL_HITS_COUNT', $article->hits); ?>
						<?php } ?>	

					</span>
					<?php endif; ?>
					<?php if ($article->params->get('access-edit')) : ?>
						<span class="list-edit pull-left width-50">
							<?php echo JHtml::_('icon.edit', $article, $params); ?>
						</span>
					<?php endif; ?>
					<strong class="list-title">
						<a href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($article->slug, $article->catid)); ?>">
							<?php echo $this->escape($article->title); ?></a>
					</strong>
					<?php if ($this->items[$i]->state == 0): ?>
						<span class="label label-warning">Unpublished</span>
					<?php endif; ?>
					<br />

						<?php if ($this->params->get('list_show_author', 1)) : ?>
						<small class="list-author">
							<?php if(!empty($article->author) || !empty($article->created_by_alias)) : ?>
								<?php $author = $article->author ?>
								<?php $author = ($article->created_by_alias ? $article->created_by_alias : $author);?>

								<?php if (!empty($article->contactid ) &&  $this->params->get('link_author') == true):?>
									<?php echo JHtml::_(
											'link',
											JRoute::_('index.php?option=com_contact&view=contact&id='.$article->contactid),
											$author
									); ?>

								<?php else :?>
									<?php echo JText::sprintf('COM_CONTENT_WRITTEN_BY', $author); ?>
								<?php endif; ?>
							<?php endif; ?>
						</small>
						<?php endif; ?>

					<?php if ($this->params->get('list_show_date')) : ?>
					<span class="list-date small pull-right">
						<?php
						echo JHtml::_(
							'date', $article->displayDate,
							$this->escape($this->params->get('date_format', JText::_('DATE_FORMAT_LC3')))
						); ?>
					</span>
					<?php endif; ?>

				<?php else : // Show unauth links. ?>
					<span>
						<?php
							echo $this->escape($article->title).' : ';
							$menu		= JFactory::getApplication()->getMenu();
							$active		= $menu->getActive();
							$itemId		= $active->id;
							$link = JRoute::_('index.php?option=com_users&view=login&Itemid='.$itemId);
							$returnURL = JRoute::_(ContentHelperRoute::getArticleRoute($article->slug));
							$fullURL = new JURI($link);
							$fullURL->setVar('return', base64_encode($returnURL));
						?>
						<a href="<?php echo $fullURL; ?>" class="register">
							<?php echo JText::_('COM_CONTENT_REGISTER_TO_READ_MORE'); ?></a>
					</span>
				<?php endif; ?>
				</li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>

<?php // Code to add a link to submit an article. ?>
<?php if ($this->category->getParams()->get('access-create')) : ?>
	<?php echo JHtml::_('icon.create', $this->category, $this->category->params); ?>
<?php  endif; ?>

<?php // Add pagination links ?>
<?php if (!empty($this->items)) : ?>
	<?php if (($this->params->def('show_pagination', 2) == 1  || ($this->params->get('show_pagination') == 2))) : ?>
	<div class="pagination">
	
		<?php if ($this->params->def('show_pagination_results', 1)) : ?>
			<p class="counter">
				<?php echo $this->pagination->getPagesCounter(); ?>
			</p>
		<?php endif; ?>

		<?php echo $this->pagination->getPagesLinks(); ?>
	</div>
	<?php endif; ?>
</form>
<?php  endif; ?>
