<?php
namespace Lumi\Core;

use Lumi\Core\Models\Revision as Model;

class Revisioner
{
    public static function create($type, $object, $child_objects = false) {
        if ( $child_objects ) {
            $serialized_children = [];
            foreach ( $child_objects as $child_object ) {
                $serialized_children[] = serialize($child_object);
            }
        }

        Model::create([
            'user_id' => \Auth::user()->id,
            'type' => $type,
            'object_id' => $object->id,
            'object' => serialize($object),
            'child_objects' => $child_objects ? json_encode($serialized_children) : null,
        ]);
    }

    public static function get($revision_id, $object_class, $object_id) {
        $item = null;

        if ( $revision_id ) {
            $revision = Model::find($revision_id);
            if ( $revision ) {
                $item = unserialize($revision->object);
            }
        }

        if ( !$item ) {
            $item = $object_class::find($object_id);
        }

        return $item;
    }
}
