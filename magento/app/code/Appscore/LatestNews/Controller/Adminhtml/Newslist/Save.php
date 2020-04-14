<?php

namespace Appscore\LatestNews\Controller\Adminhtml\Newslist;

use Appscore\LatestNews\Controller\Adminhtml\Newslist;

class Save extends Newslist
{

   /**
     * @return void
     */
   public function execute()
   {
      $isPost = $this->getRequest()->getPost();

      if ($isPost) {
         $newsModel = $this->_newslistFactory->create();
         
         if (array_key_exists('id', $this->getRequest()->getParam('news'))) {
            $newsModel->load($this->getRequest()->getParam('news')['id']);
         }

         $formData = $this->getRequest()->getParam('news');
         $categoriesId = implode(',', $formData['category_id']);
         $formData['category_id'] = $categoriesId;
         $urlkey = $this->slugify($formData['title']);
         $formData['url_key'] = $urlkey;
         $newsModel->setData($formData);
         if (isset($_FILES['image']) && isset($_FILES['image']['name']) && strlen($_FILES['image']['name'])) {
            try {
               $base_media_path = 'appscore/latestnews/images';
               $uploader = $this->uploader->create(['fileId' => 'image']);
               $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
               $imageAdapter = $this->adapterFactory->create();
               $uploader->addValidateCallback('image', $imageAdapter, 'validateUploadFile');
               $uploader->setAllowRenameFiles(true);
               $uploader->setFilesDispersion(true);
               $mediaDirectory = $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
               $result = $uploader->save($mediaDirectory->getAbsolutePath($base_media_path));
               $newsModel->setImage($base_media_path.$result['file']);
            } catch (\Exception $e) {
               if ($e->getCode() == 0) {
                  $this->messageManager->addError($e->getMessage());
               }
            }
         } else {
            if (isset($formData['image']) && isset($formData['image']['value'])) {
               if (isset($formData['image']['delete'])) {
                  $newsModel->setImage(null);
               } elseif (isset($formData['image']['value'])) {
                  $newsModel->setImage($formData['image']['value']);
               } else {
                  $newsModel->setImage(null);
               }
            }
         }
      
         try {
            $newsModel->save();

            // Display success message
            $this->messageManager->addSuccess(__('The news has been saved.'));

            // Check if 'Save and Continue'
            if ($this->getRequest()->getParam('back')) {
               $this->_redirect('*/*/edit', ['id' => $newsModel->getId(), '_current' => true]);
               return;
            }

            // Go to grid page
            $this->_redirect('*/*/');
            return;
         } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
         }

         $this->_getSession()->setFormData($formData);
         $this->_redirect('*/*/edit', ['id' => $newsModel->getId()]);
      }
   }

   function slugify($string){
      return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string), '-'));
   }
}