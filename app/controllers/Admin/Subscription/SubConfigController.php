<?php

namespace MissionNext\Controllers\Admin\Subscription;


use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use MissionNext\Controllers\Admin\AdminBaseController;
use MissionNext\Models\Subscription\SubConfig;
use MissionNext\Repos\Subscription\SubConfigRepository;
use MissionNext\Repos\Subscription\SubConfigRepositoryInterface;
use MissionNext\Validators\SubConfig as ConfValidator;

class SubConfigController extends AdminBaseController
{
    const VIEW_PREFIX = 'admin.subscription.config';

    const ROUTE_PREFIX = 'sub.config';

    public function postIndex()
    {

        $validator = new ConfValidator( $this->request );

        if (!$validator->passes())
        {

            return $this->redirect->route($this->routeName('create'))->withInput()->withErrors($validator->validator());
        }
        /** @var  $repo SubConfigRepository */
        $repo = $this->repoContainer[SubConfigRepositoryInterface::KEY];
        $repo->create($this->request->except('_token'));

        $this->session->flash('info', "Config successfully created");

        return $this->redirect->route($this->routeName('list'));
    }

    /**
     * @return \Illuminate\View\View
     */
    public function getIndex()
    {
        /** @var  $repo SubConfigRepository */
        $repo = $this->repoContainer[SubConfigRepositoryInterface::KEY];
        /** @var  $models Paginator */
        $models = $repo->getModel()->orderBy('id')->paginate(static::PAGINATE);

        return $this->view->make($this->viewTemplate('index'), ['models' => $models ]);
    }

    /**
     * @return \Illuminate\View\View
     */
    public function getCreate()
    {

        return $this->view->make($this->viewTemplate('create'));
    }

    /**
     * @param $id
     *
     * @return \Illuminate\View\View
     */
    public function getEdit($id)
    {
        /** @var  $repo SubConfigRepository */
        $repo = $this->repoContainer[SubConfigRepositoryInterface::KEY];
        $model = $repo->getModel()->findOrFail($id);
        $this->session->flash('model', $model);

        return $this->view->make($this->viewTemplate('edit'),['model' => $model] );
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postEdit($id)
    {
        $validator = new ConfValidator( $this->request );

        if (!$validator->passes())
        {

            return $this->redirect->route($this->routeName('edit'), [$id])->withInput()->withErrors($validator->validator());
        }
        /** @var  $model SubConfig */
        $model = $this->session->get('model');
        $model->update($this->request->except('_token'));
        $this->session->flash('info', "Config successfully updated");

        return $this->redirect->route($this->routeName('list'));
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function deleteIndex($id)
    {
        /** @var  $repo SubConfigRepository */
        $repo = $this->repoContainer[SubConfigRepositoryInterface::KEY];
        $model = $repo->getModel()->findOrFail($id);
        $model->delete();
        $this->session->flash('info', "Config successfully deleted");

        return $this->redirect->route($this->routeName('list'));
    }
} 