

<?php $__env->startSection('title', 'Subjects'); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <h2 class="fw-bold mb-4 text-success">Subjects</h2>

    <div class="d-flex justify-content-between mb-3">
        <?php if(auth()->user()->role === 'teacher'): ?>
            <a href="<?php echo e(route('subjects.create')); ?>" class="btn btn-success">+ Create Subject</a>
        <?php endif; ?>

        <?php if(auth()->user()->role === 'student'): ?>
            <a href="<?php echo e(route('subjects.joinForm')); ?>" class="btn btn-warning text-dark fw-bold">Join Subject</a>
        <?php endif; ?>
    </div>

    <div class="row">
        <?php $__empty_1 = true; $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="col-md-4">
            <div class="card shadow-sm mb-4" style="border-radius: 15px;">
                <div class="card-body">
                    <h5 class="fw-bold text-success"><?php echo e($subject->name); ?></h5>
                    <p class="text-muted"><?php echo e($subject->description); ?></p>

                    <p class="small text-secondary mb-2">
                        Code: <span class="fw-bold"><?php echo e($subject->code); ?></span>
                    </p>

                    <?php if(auth()->user()->role === 'teacher'): ?>
                        <a href="<?php echo e(route('subjects.missed', $subject->id)); ?>" 
                           class="btn btn-outline-danger btn-sm mt-2">View Missed Deadlines</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p>No subjects found.</p>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\quiz-system\resources\views/subjects/index.blade.php ENDPATH**/ ?>