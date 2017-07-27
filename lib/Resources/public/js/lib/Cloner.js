var action = ag.admin.EntityListTableDuplicateAction; // save some bytes
action.cloneFunc["image"] = value => action.createClone(value);
action.cloneFunc["images"] = value => value.map(child => action.createClone(child));
