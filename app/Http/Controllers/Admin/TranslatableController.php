<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Translatable\BulkDestroyTranslatable;
use App\Http\Requests\Admin\Translatable\DestroyTranslatable;
use App\Http\Requests\Admin\Translatable\IndexTranslatable;
use App\Http\Requests\Admin\Translatable\StoreTranslatable;
use App\Http\Requests\Admin\Translatable\UpdateTranslatable;
use App\Models\Translatable;
use Brackets\AdminListing\Facades\AdminListing;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TranslatableController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param IndexTranslatable $request
     * @return array|Factory|View
     */
    public function index(IndexTranslatable $request)
    {
        // create and AdminListing instance for a specific model and
        $data = AdminListing::create(Translatable::class)->processRequestAndGet(
            // pass the request with params
            $request,

            // set columns to query
            ['id', 'title', 'published_at'],

            // set columns to searchIn
            ['id', 'title', 'perex']
        );

        if ($request->ajax()) {
            if ($request->has('bulk')) {
                return [
                    'bulkItems' => $data->pluck('id')
                ];
            }
            return ['data' => $data];
        }

        return view('admin.translatable.index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function create()
    {
        $this->authorize('admin.translatable.create');

        return view('admin.translatable.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreTranslatable $request
     * @return array|RedirectResponse|Redirector
     */
    public function store(StoreTranslatable $request)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Store the Translatable
        $translatable = Translatable::create($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/translatables'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/translatables');
    }

    /**
     * Display the specified resource.
     *
     * @param Translatable $translatable
     * @throws AuthorizationException
     * @return void
     */
    public function show(Translatable $translatable)
    {
        $this->authorize('admin.translatable.show', $translatable);

        // TODO your code goes here
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Translatable $translatable
     * @throws AuthorizationException
     * @return Factory|View
     */
    public function edit(Translatable $translatable)
    {
        $this->authorize('admin.translatable.edit', $translatable);


        return view('admin.translatable.edit', [
            'translatable' => $translatable,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateTranslatable $request
     * @param Translatable $translatable
     * @return array|RedirectResponse|Redirector
     */
    public function update(UpdateTranslatable $request, Translatable $translatable)
    {
        // Sanitize input
        $sanitized = $request->getSanitized();

        // Update changed values Translatable
        $translatable->update($sanitized);

        if ($request->ajax()) {
            return [
                'redirect' => url('admin/translatables'),
                'message' => trans('brackets/admin-ui::admin.operation.succeeded'),
                'object' => $translatable
            ];
        }

        return redirect('admin/translatables');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyTranslatable $request
     * @param Translatable $translatable
     * @throws Exception
     * @return ResponseFactory|RedirectResponse|Response
     */
    public function destroy(DestroyTranslatable $request, Translatable $translatable)
    {
        $translatable->delete();

        if ($request->ajax()) {
            return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param BulkDestroyTranslatable $request
     * @throws Exception
     * @return Response|bool
     */
    public function bulkDestroy(BulkDestroyTranslatable $request) : Response
    {
        DB::transaction(static function () use ($request) {
            collect($request->data['ids'])
                ->chunk(1000)
                ->each(static function ($bulkChunk) {
                    Translatable::whereIn('id', $bulkChunk)->delete();

                    // TODO your code goes here
                });
        });

        return response(['message' => trans('brackets/admin-ui::admin.operation.succeeded')]);
    }
}
