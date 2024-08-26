import shlex
import sys
import subprocess  # nosec


class CLICommand:
    def __init__(self, executable, commandArgs, workingDir):
        self.arguments = commandArgs
        self.executable = executable
        self.workingDir = workingDir

    def go(self):
        args = [shlex.quote(self.executable)]

        for arg in self.arguments:
            args.append(shlex.quote(arg))

        proc = subprocess.run(
            args,  # nosec B603
            bufsize=0,  # nosec B603
            shell=False  # nosec B603
        )

        return proc.returncode
