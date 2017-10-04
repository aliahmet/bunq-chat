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
     *  Creates a new group
     *
     * $sample_response
     *    {
     *      "name": "Secret Birthday Party",
     *      "id": 3,
     *      "members": [
     *        {
     *          "username": "Nathan",
     *          "id": 2
     *        },
     *        {
     *          "username": "Sailor",
     *          "id": 3
     *        },
     *        {
     *          "username": "Alamet",
     *          "id": 1
     *        }
     *      ]
     *    }
     * sample_response$
     */
    public function create($request, $response, $attributes, $validated_data)
    {
        if(is_string($validated_data['users']))
            $validated_data['users'] = explode(",",$validated_data['users']);
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
     *
     * $sample_response
     *   [
     *      {
     *        "name": "sigorta",
     *        "id": 1,
     *        "members": [
     *          {
     *            "username": "jamie",
     *            "id": 2
     *          },
     *          {
     *            "username": "Alamet",
     *            "id": 1
     *          }
     *        ]
     *      },
     *      {
     *        "name": "Secret Birthday Party",
     *        "id": 3,
     *        "members": [
     *          {
     *            "username": "Nathan",
     *            "id": 2
     *          },
     *          {
     *            "username": "Sailor",
     *            "id": 3
     *          },
     *          {
     *            "username": "Alamet",
     *            "id": 1
     *          }
     *        ]
     *      }
     *    ]
     * sample_response$
     */
    public function list_groups($request, $response, $attributes, $validated_data)
    {

        return $response->withJson(User::me()->groups()->get());
    }

    /**
     * Get details of a group
     *
     * $sample_response
     *      {
     *        "name": "Secret Birthday Party",
     *        "id": 3,
     *        "members": [
     *          {
     *            "username": "Nathan",
     *            "id": 2
     *          },
     *          {
     *            "username": "Sailor",
     *            "id": 3
     *          },
     *          {
     *            "username": "Alamet",
     *            "id": 1
     *          }
     *        ]
     *      }
     * sample_response$
     */
    public function retrieve($request, $response, $attributes, $validated_data)
    {


        $id = $attributes['id'];
        $group = User::me()->groups()->find($id);
        if (!$group) {
            throw new APIException("Group doesn't exist or you are not allowed to see it!");
        }

        return $response->withJson($group);
    }

    /**
     * Add a new user to a group
     *
     * $sample_response
     *      {
     *        "name": "Secret Birthday Party",
     *        "id": 3,
     *        "members": [
     *          {
     *            "username": "Nathan",
     *            "id": 2
     *          },
     *          {
     *            "username": "Sailor",
     *            "id": 3
     *          },
     *          {
     *            "username": "Alamet",
     *            "id": 1
     *          }
     *        ]
     *      }
     * sample_response$
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
     * 
     * $sample_response
     *      {
     *        "name": "Secret Birthday Party",
     *        "id": 3,
     *        "members": [
     *          {
     *            "username": "Nathan",
     *            "id": 2
     *          },
     *          {
     *            "username": "Sailor",
     *            "id": 3
     *          },
     *          {
     *            "username": "Alamet",
     *            "id": 1
     *          }
     *        ]
     *      }
     * sample_response$
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