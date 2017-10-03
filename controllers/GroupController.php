<?php
/**
 * Created by PhpStorm.
 * User: aliahmet
 * Date: 10/2/17
 * Time: 9:35 PM
 */

namespace Controller;

use Model\Group;
use Model\User;

class GroupController
{
    /**
     *  Send a new message
     */
    public function create($request, $response, $attributes, $validated_data)
    {
        $users = User::whereIn("id", $validated_data['users'])->get();
        $group = new Group();
        $group->name = $validated_data['name'];
        $group->save();
        $group->users()->syncWithoutDetaching($users);
        $group->users()->syncWithoutDetaching(User::me());
        return $response->withJson($group, 201);
    }

    /**
     * List all groups of the user
     */
    public function list_groups($request, $response, $attributes, $validated_data)
    {

        return $response->withJson(User::me()->groups()->get(), 201);
    }

    /**
     * Get details of a group
     */
    public function retrieve($request, $response, $attributes, $validated_data)
    {


        $id = $attributes['id'];
        $group = User::me()->groups()->find($id);
        if (!$group) {
            throw new APIException("Group doesn't exist or you are not allowed to see it!");
        }

        return $response->withJson($group, 201);
    }

    /**
     * Add a new user to a group
     */
    public function add_user($request, $response, $attributes, $validated_data)
    {
        $user_id = $validated_data['user'];
        $group_id = $attributes['group_id'];

        $group = User::me()->groups()->find($group_id);
        $user = User::find($user_id);

        if (!$group) {
            throw new APIException("Group doesn't exist or you are not allowed to see it!");
        }

        if ($group->users->contains($user)) {
            throw new APIException("User is already in group!");
        }

        $group->users()->syncWithoutDetaching($user);


        return $response->withJson($group, 201);
    }

    /**
     * Remove a person from a group or Leave a group
     */
    public function remove_user($request, $response, $attributes, $validated_data)
    {
        $user_id = $validated_data['user'];
        $group_id = $attributes['group_id'];

        $group = User::me()->groups()->find($group_id);
        $user = User::find($user_id);

        if (!$group) {
            throw new APIException("Group doesn't exist or you are not allowed to see it!");
        }

        if (!$group->users->contains($user)) {
            throw new APIException("User is not in the group!");
        }

        $group->users()->syncWithoutDetaching($user);


        return $response->withJson($group, 201);
    }


}